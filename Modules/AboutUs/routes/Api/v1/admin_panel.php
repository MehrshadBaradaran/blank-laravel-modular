<?php

use Illuminate\Support\Facades\Route;
use Modules\AboutUs\app\Http\Controllers\Api\V1\AdminPanel\AboutUsController;

// AboutUs
Route::group(['prefix' => 'about-us', 'as' => 'about-us.'], function () {
    Route::controller(AboutUsController::class)->group(function () {
        Route::get('', 'index')->name('index');
        Route::put('', 'update')->name('update');
    });
});
