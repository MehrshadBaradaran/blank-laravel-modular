<?php

use Illuminate\Support\Facades\Route;
use Modules\Setting\app\Http\Controllers\Api\V1\AdminPanel\SettingController;

// Setting
Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {
    Route::controller(SettingController::class)->group(function () {
        Route::get('', 'index')->name('index');
        Route::put('', 'update')->name('update');
    });
});

