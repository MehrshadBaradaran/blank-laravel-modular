<?php

use Illuminate\Support\Facades\Route;
use Modules\TAC\app\Http\Controllers\Api\V1\App\TACController;

// TAC
Route::get('terms-and-conditions', TACController::class)->name('terms-and-conditions');
