<?php

use Illuminate\Support\Facades\Route;
use Modules\Banner\app\Http\Controllers\Api\V1\App\BannerController;

// Banner
Route::apiResource('banners', BannerController::class)->only('index', 'show');
