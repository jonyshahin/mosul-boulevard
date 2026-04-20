<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNotificationRecipientRuleRequest;
use App\Http\Requests\UpdateNotificationRecipientRuleRequest;
use App\Http\Resources\NotificationRecipientRuleResource;
use App\Models\NotificationRecipientRule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NotificationRecipientRuleController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', NotificationRecipientRule::class);

        $query = NotificationRecipientRule::query()
            ->with(['requestType', 'recipient'])
            ->orderBy('sort_order')
            ->orderBy('id');

        return NotificationRecipientRuleResource::collection(
            $query->paginate($this->perPage($request)),
        );
    }

    public function store(StoreNotificationRecipientRuleRequest $request): JsonResponse
    {
        $this->authorize('create', NotificationRecipientRule::class);

        $rule = NotificationRecipientRule::create($request->validated());
        $rule->load(['requestType', 'recipient']);

        return (new NotificationRecipientRuleResource($rule))
            ->response()
            ->setStatusCode(201);
    }

    public function update(
        UpdateNotificationRecipientRuleRequest $request,
        NotificationRecipientRule $notificationRecipientRule,
    ): NotificationRecipientRuleResource {
        $this->authorize('update', $notificationRecipientRule);

        $notificationRecipientRule->update($request->validated());
        $notificationRecipientRule->load(['requestType', 'recipient']);

        return new NotificationRecipientRuleResource($notificationRecipientRule);
    }

    public function destroy(NotificationRecipientRule $notificationRecipientRule): JsonResponse
    {
        $this->authorize('delete', $notificationRecipientRule);

        $notificationRecipientRule->delete();

        return response()->json(null, 204);
    }

    private function perPage(Request $request): int
    {
        $requested = (int) $request->input('per_page', 20);

        return max(1, min($requested, 100));
    }
}
