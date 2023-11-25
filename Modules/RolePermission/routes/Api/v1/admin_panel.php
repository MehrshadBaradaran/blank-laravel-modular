<?php

use Illuminate\Support\Facades\Route;
use Modules\RolePermission\app\Http\Controllers\Api\V1\AdminPanel\RoleController;
use Modules\RolePermission\app\Http\Controllers\Api\V1\AdminPanel\PermissionController;

// Role
Route::apiResource('roles', RoleController::class);

// Permission
Route::get('permissions', PermissionController::class)->name('permissions');
