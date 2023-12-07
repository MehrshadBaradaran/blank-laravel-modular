<?php

namespace Modules\FAQ\app\Http\Controllers\Api\V1\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\FAQ\app\Models\FAQ;
use Modules\FAQ\app\Resources\V1\App\FAQ\FAQCollection;
use Modules\FAQ\app\Resources\V1\App\FAQ\FAQResource;

class FAQController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $faqs = FAQ::query()
            ->when($request->search, function ($q, $v) {
                $q->whereLike('question', $v);
            })
            ->orderBy('sort_index')
            ->orderBy('created_at', 'desc');


        $faqs = $request->get('paginate', 'true') == 'true'
            ? $faqs->paginate($request->get('page_size'))
            : $faqs->get();

        return response()->list(FAQCollection::make($faqs)->response()->getData(true));
    }

    public function show(FAQ $faq): JsonResponse
    {
        return response()->success(data: FAQResource::make($faq));
    }
}
