<?php

use Illuminate\Support\Facades\Route;
use Modules\Notification\app\Http\Controllers\Api\V1\App\NotificationController;

// Notification
Route::apiResource('notifications', NotificationController::class)->only('index', 'show');
