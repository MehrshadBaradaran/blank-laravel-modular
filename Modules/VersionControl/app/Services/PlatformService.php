<?php

namespace Modules\VersionControl\app\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Spy\app\Utilities\SpyLogger;
use Modules\VersionControl\app\Models\Platform;

class PlatformService
{
    protected string $permissionGroup = 'platform';
    protected string $name = 'platform';

    public function getAlias(): string
    {
        return __('versioncontrol::aliases.name.platform');
    }

    public function create(array $data): Platform
    {
        return DB::transaction(function () use ($data) {
            $platform = Platform::create($data);

            (new SpyLogger())
                ->userId(Auth::id())
                ->title("create $this->name")
                ->target($platform)
                ->permissionName("admin_panel.$this->permissionGroup.create")
                ->action('create')
                ->submit();

            return $platform;
        });
    }

    public function update(Platform $platform, array $data): Platform
    {
        DB::transaction(function () use ($platform, $data) {
            $platform->update($data);

            (new SpyLogger())
                ->userId(Auth::id())
                ->title("update $this->name")
                ->target($platform)
                ->permissionName("admin_panel.$this->permissionGroup.update")
                ->action('update')
                ->submit();
        });

        return $platform;
    }

    public function changeStatus(Platform $platform, mixed $status): Platform
    {
        return DB::transaction(function () use ($platform, $status) {
            Platform::withoutEvents(function () use ($platform, $status) {
                $platform->update(['status' => $status,]);
            });

            (new SpyLogger())
                ->userId(Auth::id())
                ->title("change $this->name status")
                ->description("change $this->name status to {$platform->status->getText()}")
                ->target($platform)
                ->permissionName("admin_panel.$this->permissionGroup.status")
                ->action('status')
                ->submit();

            return $platform;
        });
    }

    public function delete(Platform $platform): bool
    {
        return DB::transaction(function () use ($platform) {
            $result = $platform->delete();

            (new SpyLogger())
                ->userId(Auth::id())
                ->title("delete $this->name")
                ->target($platform)
                ->permissionName("admin_panel.$this->permissionGroup.delete")
                ->action('delete')
                ->submit();

            return $result;
        });
    }
}
