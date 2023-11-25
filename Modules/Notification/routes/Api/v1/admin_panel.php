<?php

use Illuminate\Support\Facades\Route;
use Modules\Notification\app\Http\Controllers\Api\V1\AdminPanel\NotificationController;

// Notification
Route::apiResource('notifications', NotificationController::class);
Route::group(['prefix' => 'notifications/{notification}/', 'as' => 'notifications.'], function () {
    Route::controller(NotificationController::class)->group(function () {
        Route::put('status-change', 'changeStatus')->name('status-change');
    });
});
