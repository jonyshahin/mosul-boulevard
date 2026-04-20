<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\RequestSeverity;
use App\Enums\RequestStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\AssignInspectionRequestRequest;
use App\Http\Requests\StoreInspectionRequestRequest;
use App\Http\Requests\TransitionInspectionRequestRequest;
use App\Http\Requests\UpdateInspectionRequestRequest;
use App\Http\Resources\InspectionRequestDetailResource;
use App\Http\Resources\InspectionRequestResource;
use App\Models\InspectionRequest;
use App\Models\TowerUnit;
use App\Models\User;
use App\Models\Villa;
use App\Services\InspectionRequestService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class InspectionRequestController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly InspectionRequestService $service,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', InspectionRequest::class);

        $query = InspectionRequest::query()
            ->with(['requester', 'assignee', 'verifiedBy', 'requestType', 'subject'])
            ->withCount(['replies', 'media']);

        $this->applyFilters($query, $request);
        $this->applySort($query, $request);

        return InspectionRequestResource::collection(
            $query->paginate($this->perPage($request)),
        );
    }

    public function store(StoreInspectionRequestRequest $request): JsonResponse
    {
        $this->authorize('create', InspectionRequest::class);

        $data = $request->validated();
        $files = $request->file('media', []);

        $model = $this->service->create($data, $request->user(), is_array($files) ? $files : []);

        $model->load(['requester', 'assignee', 'requestType', 'subject', 'media'])
            ->loadCount(['replies', 'media']);

        return (new InspectionRequestDetailResource($model))
            ->response()
            ->setStatusCode(201);
    }

    public function show(InspectionRequest $inspectionRequest): InspectionRequestDetailResource
    {
        $this->authorize('view', $inspectionRequest);

        $inspectionRequest->load([
            'requester',
            'assignee',
            'verifiedBy',
            'requestType',
            'subject',
            'media.uploader',
            'replies.author',
            'replies.media.uploader',
        ])->loadCount(['replies', 'media']);

        return new InspectionRequestDetailResource($inspectionRequest);
    }

    public function update(
        UpdateInspectionRequestRequest $request,
        InspectionRequest $inspectionRequest,
    ): InspectionRequestResource {
        $this->authorize('update', $inspectionRequest);

        $updated = $this->service->update($inspectionRequest, $request->validated());
        $updated->load(['requester', 'assignee', 'requestType', 'subject'])
            ->loadCount(['replies', 'media']);

        return new InspectionRequestResource($updated);
    }

    public function destroy(InspectionRequest $inspectionRequest): JsonResponse
    {
        $this->authorize('delete', $inspectionRequest);

        $this->service->destroy($inspectionRequest);

        return response()->json(null, 204);
    }

    public function transition(
        TransitionInspectionRequestRequest $request,
        InspectionRequest $inspectionRequest,
    ): InspectionRequestDetailResource {
        $target = RequestStatus::from($request->validated('target_status'));

        $this->authorize('transition', [$inspectionRequest, $target]);

        $updated = $this->service->transition(
            $inspectionRequest,
            $target,
            $request->user(),
            $request->validated('note'),
        );

        $updated->load(['requester', 'assignee', 'verifiedBy', 'requestType', 'subject', 'replies.author'])
            ->loadCount(['replies', 'media']);

        return new InspectionRequestDetailResource($updated);
    }

    public function assign(
        AssignInspectionRequestRequest $request,
        InspectionRequest $inspectionRequest,
    ): InspectionRequestResource {
        $this->authorize('assign', $inspectionRequest);

        $assignee = User::findOrFail($request->validated('assignee_id'));

        $updated = $this->service->assign($inspectionRequest, $assignee, $request->user());
        $updated->load(['requester', 'assignee', 'requestType', 'subject'])
            ->loadCount(['replies', 'media']);

        return new InspectionRequestResource($updated);
    }

    public function myAssignments(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', InspectionRequest::class);

        $user = $request->user();

        $query = InspectionRequest::query()
            ->where('assignee_id', $user->id)
            ->whereIn('status', [RequestStatus::Open->value, RequestStatus::InProgress->value])
            ->with(['requester', 'assignee', 'requestType', 'subject'])
            ->withCount(['replies', 'media'])
            ->orderBy('created_at', 'desc');

        return InspectionRequestResource::collection(
            $query->paginate($this->perPage($request)),
        );
    }

    public function stats(Request $request): JsonResponse
    {
        $this->authorize('viewAny', InspectionRequest::class);

        $byStatus = InspectionRequest::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->all();

        $bySeverity = InspectionRequest::query()
            ->selectRaw('severity, COUNT(*) as total')
            ->groupBy('severity')
            ->pluck('total', 'severity')
            ->all();

        $byRequestType = InspectionRequest::query()
            ->selectRaw('request_type_id, COUNT(*) as total')
            ->groupBy('request_type_id')
            ->pluck('total', 'request_type_id')
            ->all();

        $overdue = InspectionRequest::overdue()->count();

        return response()->json([
            'data' => [
                'by_status' => $this->fillZeros($byStatus, RequestStatus::cases()),
                'by_severity' => $this->fillZeros($bySeverity, RequestSeverity::cases()),
                'by_request_type' => array_map('intval', $byRequestType),
                'overdue' => $overdue,
            ],
        ]);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<InspectionRequest>  $query
     */
    private function applyFilters($query, Request $request): void
    {
        if ($statuses = (array) $request->input('status', [])) {
            $query->whereIn('status', array_filter($statuses));
        }

        if ($severities = (array) $request->input('severity', [])) {
            $query->whereIn('severity', array_filter($severities));
        }

        if ($request->filled('request_type_id')) {
            $query->where('request_type_id', $request->integer('request_type_id'));
        }

        if ($request->filled('assignee_id')) {
            $query->where('assignee_id', $request->integer('assignee_id'));
        }

        if ($request->filled('requester_id')) {
            $query->where('requester_id', $request->integer('requester_id'));
        }

        if ($request->filled('subject_type') && $request->filled('subject_id')) {
            $class = match ($request->input('subject_type')) {
                'tower_unit' => TowerUnit::class,
                'villa' => Villa::class,
                default => null,
            };

            if ($class !== null) {
                $query->where('subject_type', (new $class)->getMorphClass())
                    ->where('subject_id', $request->integer('subject_id'));
            }
        }

        if ($request->boolean('overdue')) {
            $query->overdue();
        }

        if ($request->filled('search')) {
            $term = '%'.$request->input('search').'%';
            $query->where(function ($q) use ($term): void {
                $q->where('title', 'like', $term)
                    ->orWhere('description', 'like', $term);
            });
        }
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<InspectionRequest>  $query
     */
    private function applySort($query, Request $request): void
    {
        $sort = $request->input('sort', '-created_at');
        $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
        $column = ltrim($sort, '-');

        $allowed = ['created_at', 'due_date', 'severity'];

        if (! in_array($column, $allowed, true)) {
            $column = 'created_at';
            $direction = 'desc';
        }

        $query->orderBy($column, $direction);
    }

    private function perPage(Request $request): int
    {
        $requested = (int) $request->input('per_page', 20);

        return max(1, min($requested, 100));
    }

    /**
     * @param  array<string, int>  $counts
     * @param  array<int, \BackedEnum>  $cases
     * @return array<string, int>
     */
    private function fillZeros(array $counts, array $cases): array
    {
        $out = [];
        foreach ($cases as $case) {
            $out[$case->value] = (int) ($counts[$case->value] ?? 0);
        }

        return $out;
    }
}
