<?php

use Illuminate\Support\Facades\Route;
use Modules\ContactUs\app\Http\Controllers\Api\V1\App\ContactUsController;

// ContactUs
Route::get('contact-us', ContactUsController::class)->name('contact-us');
