<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\RequestStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRequestReplyRequest;
use App\Http\Requests\TransitionInspectionRequestRequest;
use App\Http\Resources\InspectionRequestDetailResource;
use App\Http\Resources\InspectionRequestResource;
use App\Models\InspectionRequest;
use App\Models\RequestReply;
use App\Models\TowerUnit;
use App\Models\Villa;
use App\Services\InspectionRequestService;
use App\Services\RequestReplyService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class InspectionRequestsPageController extends Controller
{
    public function index(Request $request): Response
    {
        $this->denyCustomers($request);

        $user = $request->user();
        $isFirstVisit = empty(array_filter($request->query() ?: []));

        // Engineers default to "assigned to me" when hitting the list with no query.
        $assignedToMe = $request->has('assigned_to_me')
            ? $request->boolean('assigned_to_me')
            : ($isFirstVisit && $user?->isEngineer());

        $statuses = array_values(array_filter((array) $request->input('status', [])));
        $severities = array_values(array_filter((array) $request->input('severity', [])));
        $subjectType = in_array($request->input('subject_type'), ['villa', 'tower_unit'], true)
            ? $request->input('subject_type')
            : null;
        $overdueOnly = $request->boolean('overdue_only');
        $sort = $request->input('sort', '-created_at');

        $query = InspectionRequest::query()
            ->with(['requester', 'assignee', 'requestType', 'subject'])
            ->withCount(['replies', 'media']);

        if ($statuses) {
            $query->whereIn('status', $statuses);
        }

        if ($severities) {
            $query->whereIn('severity', $severities);
        }

        if ($subjectType === 'villa') {
            $query->where('subject_type', (new Villa)->getMorphClass());
        } elseif ($subjectType === 'tower_unit') {
            $query->where('subject_type', (new TowerUnit)->getMorphClass());
        }

        if ($assignedToMe && $user) {
            $query->where('assignee_id', $user->id);
        }

        if ($overdueOnly) {
            $query->overdue();
        }

        $this->applySort($query, $sort);

        $paginated = $query->paginate(20)->withQueryString();

        return Inertia::render('dashboard/inspection-requests/Index', [
            'requests' => InspectionRequestResource::collection($paginated),
            'filters' => [
                'status' => $statuses,
                'severity' => $severities,
                'subject_type' => $subjectType,
                'assigned_to_me' => $assignedToMe,
                'overdue_only' => $overdueOnly,
                'sort' => $sort,
            ],
            'translations' => $this->listTranslations(),
            'auth' => [
                'id' => $user?->id,
                'can_create' => $user ? ($user->isAdmin() || $user->isEngineer()) : false,
            ],
        ]);
    }

    public function create(Request $request): Response
    {
        $this->denyCustomers($request);

        return Inertia::render('dashboard/inspection-requests/Create', [
            'translations' => [
                'title' => __('inspection_requests.pages.create.title'),
                'coming_soon' => __('inspection_requests.pages.coming_soon'),
            ],
        ]);
    }

    public function show(Request $request, int $id): Response
    {
        $this->denyCustomers($request);

        $inspection = InspectionRequest::findOrFail($id);
        Gate::forUser($request->user())->authorize('view', $inspection);

        $inspection->load([
            'requester',
            'assignee',
            'verifiedBy',
            'requestType',
            'subject',
            'replies.author',
        ])->loadCount(['replies', 'media']);

        return Inertia::render('dashboard/inspection-requests/Show', [
            'request' => (new InspectionRequestDetailResource($inspection))->resolve($request),
            'translations' => $this->detailTranslations($inspection->id),
            'auth' => $this->authAbilities($request, $inspection),
        ]);
    }

    public function edit(Request $request, int $id): Response
    {
        $this->denyCustomers($request);

        return Inertia::render('dashboard/inspection-requests/Edit', [
            'id' => $id,
            'translations' => [
                'title' => __('inspection_requests.pages.edit.title'),
                'coming_soon' => __('inspection_requests.pages.coming_soon'),
            ],
        ]);
    }

    public function storeReply(
        StoreRequestReplyRequest $request,
        InspectionRequest $inspectionRequest,
        RequestReplyService $service,
    ): RedirectResponse {
        $this->denyCustomers($request);
        Gate::forUser($request->user())->authorize('create', [RequestReply::class, $inspectionRequest]);

        $service->create(
            $inspectionRequest,
            $request->validated(),
            $request->user(),
        );

        return back()->with('success', __('inspection_requests.detail.reply_form.success_toast'));
    }

    public function transition(
        TransitionInspectionRequestRequest $request,
        InspectionRequest $inspectionRequest,
        InspectionRequestService $service,
    ): RedirectResponse {
        $this->denyCustomers($request);
        $target = RequestStatus::from($request->validated('target_status'));
        Gate::forUser($request->user())->authorize('transition', [$inspectionRequest, $target]);

        $service->transition(
            $inspectionRequest,
            $target,
            $request->user(),
            $request->validated('note'),
        );

        return back()->with('success', __('inspection_requests.detail.transitions.success_toast'));
    }

    /**
     * @param  Builder<InspectionRequest>  $query
     */
    private function applySort(Builder $query, string $sort): void
    {
        $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
        $column = ltrim($sort, '-');

        if ($column === 'severity') {
            $query->orderByRaw(
                "CASE severity WHEN 'critical' THEN 4 WHEN 'high' THEN 3 WHEN 'medium' THEN 2 WHEN 'low' THEN 1 ELSE 0 END ".$direction,
            );
            $query->orderBy('created_at', 'desc');

            return;
        }

        if ($column === 'due_date') {
            $query->orderByRaw('due_date IS NULL, due_date '.$direction);

            return;
        }

        if (! in_array($column, ['created_at', 'updated_at'], true)) {
            $column = 'created_at';
            $direction = 'desc';
        }

        $query->orderBy($column, $direction);
    }

    /**
     * @return array<string, mixed>
     */
    private function listTranslations(): array
    {
        return [
            'title' => __('inspection_requests.pages.index.title'),
            'list' => __('inspection_requests.list'),
            'shared' => __('inspection_requests.shared'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function detailTranslations(int $id): array
    {
        return [
            'title' => __('inspection_requests.pages.show.title', ['id' => $id]),
            'detail' => __('inspection_requests.detail'),
            'shared' => __('inspection_requests.shared'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function authAbilities(Request $request, InspectionRequest $inspection): array
    {
        $user = $request->user();
        $gate = Gate::forUser($user);

        $currentStatus = $inspection->status instanceof RequestStatus
            ? $inspection->status
            : RequestStatus::from((string) $inspection->status);

        $transitions = [];
        foreach ($currentStatus->canTransitionToList() as $target) {
            $transitions[$target->value] = $gate->allows('transition', [$inspection, $target]);
        }

        return [
            'id' => $user?->id,
            'can_reply' => $gate->allows('create', [RequestReply::class, $inspection]),
            'can_update' => $gate->allows('update', $inspection),
            'can_delete' => $gate->allows('delete', $inspection),
            'can_transition_to' => $transitions,
        ];
    }

    private function denyCustomers(Request $request): void
    {
        abort_if($request->user()?->isCustomer() ?? true, 403);
    }
}
