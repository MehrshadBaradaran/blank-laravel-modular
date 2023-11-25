<?php

use Illuminate\Support\Facades\Route;
use Modules\Authentication\app\Http\Controllers\Api\V1\AuthController;

// App Routes
Route::controller(AuthController::class)->group(function () {
    Route::group(['middleware' => 'guest'], function () {
        Route::post('phone-status', 'phoneStatus')->name('phone-status');
        Route::post('phone-verify', 'phoneVerify')->name('phone-verify');
        Route::post('get-otp', 'getOTP')->name('get-otp');
        Route::post('login-otp', 'loginWithOTP')->name('login-otp');
        Route::post('login-password', 'loginWithPassword')->name('login-password');
        Route::post('pass-reset', 'passwordReset')->name('pass-reset');
        Route::post('register', 'register')->name('register');
    });

    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('me', 'user')->name('user');
        Route::get('logout', 'logout')->name('logout');
    });
});
