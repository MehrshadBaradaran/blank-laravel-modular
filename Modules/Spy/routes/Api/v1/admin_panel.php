<?php

use Illuminate\Support\Facades\Route;
use Modules\Spy\app\Http\Controllers\Api\V1\AdminPanel\SpyController;

// Spy Logs
Route::apiResource('spy-logs', SpyController::class)->only('index', 'show');
