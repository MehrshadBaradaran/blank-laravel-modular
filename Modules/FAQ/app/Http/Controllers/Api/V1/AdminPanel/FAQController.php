<?php

namespace Modules\FAQ\app\Http\Controllers\Api\V1\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
    protected string $permissionPrefix = 'admin_panel.faq';

    public function index(Request $request): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.view"]);

        $faqs = FAQ::query()
            ->when($request->search, function ($q, $v) {
                $q->whereLike('question', $v);
            })
            ->when($request->status, function ($q, $v) {
                $q->where('status', $v == 'true');
            })
            ->orderBy('sort_index')
            ->orderBy('created_at', 'desc');

        $faqs = $request->get('paginate', 'true') == 'true'
            ? $faqs->paginate($request->get('page_size'))
            : $faqs->get();

        return response()->list(FAQCollection::make($faqs)->response()->getData(true));
    }

    public function store(FAQStoreRequest $request, FAQService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.create"]);

        try {
            $faq = $service->create($request->getSafeData());

            return response()->success(
                message: __('messages.store.success', ['attribute' => $service->getAlias(),]),
                data: FAQResource::make($faq)
            );
        } catch (Exception $e) {
            return response()->error($e, __('messages.store.failure', ['attribute' => $service->getAlias(),]));
        }
    }

    public function show(FAQ $faq): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.view"]);

        return response()->success(data: FAQResource::make($faq));
    }

    public function update(FAQUpdateRequest $request, FAQ $faq, FAQService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.update"]);

        try {
            $service->update($faq, $request->getSafeData());

            return response()->success(
                message: __('messages.update.success', ['attribute' => $service->getAlias(),]),
                data: FAQResource::make($faq)
            );
        } catch (Exception $e) {
            return response()->error($e, __('messages.update.failure', ['attribute' => $service->getAlias(),]));
        }
    }

    public function destroy(FAQ $faq, FAQService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.delete"]);

        try {
            $service->delete($faq);

            return response()->success(
                message: __('messages.delete.success', ['attribute' => $service->getAlias(),]),
            );
        } catch (Exception $e) {
            return response()->error($e, __('messages.delete.failure', ['attribute' => $service->getAlias(),]));
        }
    }

    public function changeStatus(FAQ $faq, FAQService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.change-status"]);

        try {
            $faq = $service->changeStatus($faq, !$faq->status->value);

            return response()->success(
                message: __('messages.status-change.success', [
                    'attribute' => $service->getAlias(),
                    'status' => $faq->status->getAlias(),
                ]),
                data: FAQResource::make($faq)
            );
        } catch (Exception $e) {
            return response()->error($e, __('messages.status-change.failure', ['attribute' => $service->getAlias(),]));
        }
    }

    public function sort(FAQSortRequest $request, FAQService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.sort"]);

        try {
            $service->sort($request->ids);

            return response()->success(__('messages.sort.success'));

        } catch (Exception $e) {
            return response()->error($e, __('messages.sort.failure', ['attribute' => $service->getAlias(),]));
        }
    }
}
