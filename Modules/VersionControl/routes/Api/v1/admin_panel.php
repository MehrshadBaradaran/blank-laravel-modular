<?php

use Illuminate\Support\Facades\Route;
use Modules\VersionControl\app\Http\Controllers\Api\V1\AdminPanel\PlatformController;
use Modules\VersionControl\app\Http\Controllers\Api\V1\AdminPanel\VersionController;

// Platforms
Route::apiResource('platforms', PlatformController::class);
Route::group(['prefix' => 'platforms/{platform}', 'as' => 'platforms.'], function () {
    Route::controller(PlatformController::class)->group(function () {
        Route::put('status-change', 'changeStatus')->name('status-change');
    });
});

// Versions
Route::apiResource('versions', VersionController::class);
