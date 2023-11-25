<?php

use Illuminate\Support\Facades\Route;
use Modules\User\app\Http\Controllers\Api\V1\App\UserController;

// User
Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
    Route::controller(UserController::class)->group(function () {
        Route::put('', 'update')->name('update');
        Route::put('password-update', 'passwordUpdate')->name('password-update');
    });
});
