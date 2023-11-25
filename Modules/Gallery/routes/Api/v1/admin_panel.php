<?php

use Illuminate\Support\Facades\Route;
use Modules\Gallery\app\Http\Controllers\Api\V1\AdminPanel\GallerySettingController;
use Modules\Gallery\app\Http\Controllers\Api\V1\AdminPanel\ImageGalleryController;
use Modules\Gallery\app\Http\Controllers\Api\V1\AdminPanel\VideoGalleryController;

// Image Gallery
Route::apiResource('images', ImageGalleryController::class)->except('update');

// Video Gallery
Route::apiResource('videos', VideoGalleryController::class)->except('update');

// Gallery Setting
Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {
    Route::controller(GallerySettingController::class)->group(function () {
        Route::get('', 'index')->name('index');
        Route::put('', 'update')->name('update');
    });
});
