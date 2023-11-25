<?php

use Illuminate\Support\Facades\Route;
use Modules\FAQ\app\Http\Controllers\Api\V1\AdminPanel\FAQController;

// FAQ
Route::put('faqs/sort', [FAQController::class, 'sort'])->name('faqs.sort');
Route::apiResource('faqs', FAQController::class);
Route::group(['prefix' => 'faqs/{faq}/', 'as' => 'faq.'], function () {
    Route::controller(FAQController::class)->group(function () {
        Route::put('status-change', 'changeStatus')->name('status-change');
    });
});
