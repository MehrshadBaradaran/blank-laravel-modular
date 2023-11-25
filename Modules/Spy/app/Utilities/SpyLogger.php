<?php

namespace Modules\Spy\app\Utilities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Auth;
use hisorange\BrowserDetect\Parser as Browser;
use Modules\RolePermission\app\Models\Permission;
use Modules\Spy\app\Enums\SpyActionEnum;
use Modules\Spy\app\Services\SpyService;
use Modules\User\app\Models\User;
use Exception;

class SpyLogger
{
    protected Request $request;

    protected SpyActionEnum $action;

    protected ?string $permissionName = null;
    protected ?string $targetModel = null;
    protected ?string $title = null;
    protected ?string $alias = null;
    protected ?string $description = null;

    protected ?int $userId = null;
    protected ?int $targetId = null;

    public function __construct()
    {
        $this->request = request();
        $this->userId = Auth::id();
        $this->action = SpyActionEnum::OTHER;
        $this->userDatadata = [];
    }

    protected function getDeviceData(): array
    {
        $deviceData = (new Browser())->detect();
        return [
            'is_mobile' => $deviceData->isMobile(),
            'is_tablet' => $deviceData->isTablet(),
            'is_desktop' => $deviceData->isDesktop(),
            'is_bot' => $deviceData->isBot(),
            'device_family' => $deviceData->deviceFamily(),
            'device_model' => $deviceData->deviceModel(),
            'browser' => [
                'name' => $deviceData->browserName(),
                'family' => $deviceData->browserFamily(),
                'version' => $deviceData->browserVersion(),
            ],
            'platform' => [
                'name' => $deviceData->platformName(),
                'family' => $deviceData->platformFamily(),
                'version' => $deviceData->platformVersion(),
            ],
        ];
    }

    protected function getSpiedData(): array
    {
        return [
            'user_id' => $this->userId,
            'target_id' => $this->targetId,
            'target_type' => $this->targetModel,
            'permission_id' => Permission::whereName($this->permissionName)->first()?->id,

            'title' => $this->title,
            'description' => $this->description,

            'request_method' => $this->request->method(),
            'request_url' => $this->request->url(),
            'ip_address' => $this->request->getClientIp(),
            'client_app_version' => $this->request->header('client-application-version'),

            'action' => $this->action,

            'request_data' => $this->request->all(),
            'request_device_data' => $this->getDeviceData(),
            'user_data' => User::find($this->userId)?->toArray(),
            'target_data' => $this->targetModel
                ? $this->targetModel::find($this->targetId)?->toArray()
                : null,
        ];
    }

    public function submit(): void
    {
        if (config('spy.status')) {
            (new SpyService())->store($this->getSpiedData());
        }
    }

    public function userId(int $userId): static
    {
        $this->userId = $userId;
        return $this;
    }

    public function permissionName(string $permissionName): static
    {
        $this->permissionName = $permissionName;
        return $this;
    }

    public function action(string $action): static
    {
        if (!in_array($action, SpyActionEnum::getValues())) {
            throw new Exception('Wrong Action!');
        }

        $this->action = SpyActionEnum::getCaseByValue($action);
        return $this;
    }

    public function target(Model $target): static
    {
        $this->targetModel = get_class($target);
        $this->targetId = $target->id;
        return $this;
    }

    public function title(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function alias(string $alias): static
    {
        $this->alias = $alias;
        return $this;
    }

    public function description(string $description): static
    {
        $this->description = $description;
        return $this;
    }
}
