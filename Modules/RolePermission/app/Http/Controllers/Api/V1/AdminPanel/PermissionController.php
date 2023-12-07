<?php

namespace Modules\RolePermission\app\Http\Controllers\Api\V1\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\RolePermission\app\Models\PermissionType;
use Modules\RolePermission\app\Resources\V1\AdminPanel\PermissionType\PermissionTypeCollection;

class PermissionController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        return response()->success(data: PermissionTypeCollection::make(PermissionType::all()));
    }
}
