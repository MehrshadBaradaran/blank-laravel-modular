<?php

use Illuminate\Support\Facades\Route;
use Modules\TAC\app\Http\Controllers\Api\V1\AdminPanel\TACController;

// TAC
Route::group(['prefix' => 'terms-and-conditions', 'as' => 'terms-and-conditions.'], function () {
    Route::controller(TACController::class)->group(function () {
        Route::get('', 'index')->name('index');
        Route::put('', 'update')->name('update');
    });
});
