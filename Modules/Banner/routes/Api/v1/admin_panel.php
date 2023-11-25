<?php

use Illuminate\Support\Facades\Route;
use Modules\Banner\app\Http\Controllers\Api\V1\AdminPanel\BannerController;

// Banner
Route::apiResource('banners', BannerController::class);
Route::group(['prefix' => 'banners/{banner}/', 'as' => 'banners.'], function () {
    Route::controller(BannerController::class)->group(function () {
        Route::put('status-change', 'changeStatus')->name('status-change');
    });
});
