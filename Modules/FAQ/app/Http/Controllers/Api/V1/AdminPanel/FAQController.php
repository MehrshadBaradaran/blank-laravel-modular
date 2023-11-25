<?php

namespace Modules\FAQ\app\Http\Controllers\Api\V1\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Modules\FAQ\app\Http\Requests\Api\V1\AdminPanel\FAQ\FAQSortRequest;
use Modules\FAQ\app\Http\Requests\Api\V1\AdminPanel\FAQ\FAQStoreRequest;
use Modules\FAQ\app\Http\Requests\Api\V1\AdminPanel\FAQ\FAQUpdateRequest;
use Modules\FAQ\app\Models\FAQ;
use Modules\FAQ\app\Resources\V1\AdminPanel\FAQ\FAQCollection;
use Modules\FAQ\app\Resources\V1\AdminPanel\FAQ\FAQResource;
use Modules\FAQ\app\Services\FAQService;
use Modules\RolePermission\app\Models\Permission;

class FAQController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, 'admin_panel.faq.view']);

        $faqs = FAQ::query()
            ->when($request->search, function ($q, $value) {
                $q->where('question', 'LIKE', "%$value%");
            })
            ->when($request->status, function ($q, $value) {
                $q->where('status', $value == 'true');
            })
            ->orderBy('sort_index', 'desc')
            ->orderBy('created_at', 'desc');


        $faqs = $request->get('paginate', 'true') == 'true'
            ? $faqs->paginate($request->get('page_size'))
            : $faqs->get();

        return response()->json((new FAQCollection($faqs))->response()->getData(true));
    }

    public function store(FAQStoreRequest $request, FAQService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, 'admin_panel.faq.create']);

        try {
            $faq = $service->create($request->getSafeData());

            return response()->json([
                'message' => __('messages.store.success', ['attribute' => $service->getAlias(),]),
                'data' => new FAQResource($faq),
            ]);

        } catch (Exception $exception) {
            Log::channel('report')->error('FAQ store: ' . $exception->getMessage());
            return response()->json([
                'message' => __('messages.store.failure', ['attribute' => $service->getAlias(),]),
            ], 500);
        }
    }

    public function show(FAQ $faq): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, 'admin_panel.faq.view']);

        return response()->json([
            'data' => new FAQResource($faq),
        ]);
    }

    public function update(FAQUpdateRequest $request, FAQ $faq, FAQService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, 'admin_panel.faq.update']);

        try {
            $service->update($faq, $request->getSafeData());

            return response()->json([
                'message' => __('messages.update.success', ['attribute' => $service->getAlias(),]),
                'data' => new FAQResource($faq),
            ]);

        } catch (Exception $exception) {
            Log::channel('report')->error('FAQ update: ' . $exception->getMessage());
            return response()->json([
                'message' => __('messages.update.failure', ['attribute' => $service->getAlias(),]),
            ], 500);
        }
    }

    public function destroy(FAQ $faq, FAQService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, 'admin_panel.faq.delete']);

        try {
            $service->delete($faq);

            return response()->json([
                'message' => __('messages.delete.success', ['attribute' => $service->getAlias(),]),
            ]);
        } catch (Exception $exception) {
            Log::channel('report')->error('FAQ destroy: ' . $exception->getMessage());
            return response()->json([
                'message' => __('messages.delete.failure', ['attribute' => $service->getAlias(),]),
            ], 500);
        }
    }

    public function changeStatus(FAQ $faq, FAQService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, 'admin_panel.faq.change-status']);

        try {
            $faq = $service->changeStatus($faq, !$faq->status->value);

            return response()->json([
                'message' => __('messages.status-change.success', [
                    'attribute' => $service->getAlias(),
                    'status' => $faq->status->getAlias(),
                ]),
            ]);
        } catch (Exception $exception) {
            Log::channel('report')->error('FAQ status: ' . $exception->getMessage());
            return response()->json([
                'message' => __('messages.status-change.failure'),
            ], 500);
        }
    }

    public function sort(FAQSortRequest $request, FAQService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, 'admin_panel.faq.sort']);

        try {
            $service->sort($request->ids);

            return response()->json([
                'message' => __('messages.sort.success'),
            ]);

        } catch (Exception $exception) {
            Log::channel('report')->error("$this->alias reorder: " . $exception->getMessage());
            return response()->json([
                'message' => __('messages.sort.failure'),
            ], 500);
        }
    }
}
