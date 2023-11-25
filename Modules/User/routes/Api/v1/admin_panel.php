<?php

use Illuminate\Support\Facades\Route;
use Modules\User\app\Http\Controllers\Api\V1\AdminPanel\UserController;

// User
Route::apiResource('users', UserController::class);
Route::group(['prefix' => 'users/{user}/', 'as' => 'users.'], function () {
    Route::controller(UserController::class)->group(function () {
        Route::put('role-change', 'roleChange')->name('role-change');
        Route::put('status-change', 'statusChange')->name('status-change');
    });
});

