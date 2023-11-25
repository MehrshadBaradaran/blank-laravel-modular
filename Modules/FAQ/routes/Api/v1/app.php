<?php

use Illuminate\Support\Facades\Route;
use Modules\FAQ\app\Http\Controllers\Api\V1\App\FAQController;

// FAQ
Route::apiResource('faqs', FAQController::class)->only('index', 'show');
