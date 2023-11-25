<?php

use Illuminate\Support\Facades\Route;
use Modules\Setting\app\Http\Controllers\Api\V1\App\SettingController;

// Setting
Route::get('settings', SettingController::class)->name('settings');
