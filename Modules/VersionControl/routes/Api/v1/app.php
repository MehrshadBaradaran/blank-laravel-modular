<?php

use Illuminate\Support\Facades\Route;
use Modules\VersionControl\app\Http\Controllers\Api\V1\App\VersionController;

// Versions
Route::get('versions/{os}/{version}', VersionController::class)->name('versions.index');
