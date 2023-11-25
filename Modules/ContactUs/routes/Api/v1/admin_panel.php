<?php

use Illuminate\Support\Facades\Route;
use Modules\ContactUs\app\Http\Controllers\Api\V1\AdminPanel\ContactUsController;

// ContactUs
Route::group(['prefix' => 'contact-us', 'as' => 'contact-us.'], function () {
    Route::controller(ContactUsController::class)->group(function () {
        Route::get('', 'index')->name('index');
        Route::put('', 'update')->name('update');
    });
});
