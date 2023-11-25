<?php

use Illuminate\Support\Facades\Route;
use Modules\AboutUs\app\Http\Controllers\Api\V1\App\AboutUsController;

// AboutUs
Route::get('about-us', AboutUsController::class)->name('about-us');
