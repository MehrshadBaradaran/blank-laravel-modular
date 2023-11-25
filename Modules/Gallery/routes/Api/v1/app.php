<?php

use Illuminate\Support\Facades\Route;
use Modules\Gallery\app\Http\Controllers\Api\V1\App\GallerySettingController;
use Modules\Gallery\app\Http\Controllers\Api\V1\App\ImageGalleryController;
use Modules\Gallery\app\Http\Controllers\Api\V1\App\VideoGalleryController;

// GallerySetting
Route::get('settings', GallerySettingController::class)->name('settings');

// Image Gallery
Route::apiResource('images', ImageGalleryController::class)->except('update');

// Video Gallery
Route::apiResource('videos', VideoGalleryController::class)->except('update');
