<?php

namespace Modules\Banner\app\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Banner\app\Models\Banner;
use Modules\Spy\app\Utilities\SpyLogger;

class BannerService
{
    protected string $permissionGroup = 'banner';
    protected string $name = 'banner';

    public function getAlias(): string
    {
        return __('banner::aliases.name.banner');
    }

    public function create(array $data): Banner
    {
        return DB::transaction(function () use ($data) {
            $banner = Banner::create($data);

            (new SpyLogger())
                ->userId(Auth::id())
                ->title("create $this->name")
                ->target($banner)
                ->permissionName("admin_panel.$this->permissionGroup.create")
                ->action('create')
                ->submit();

            return $banner;
        });
    }

    public function update(Banner $banner, array $data): Banner
    {
        DB::transaction(function () use ($banner, $data) {
            $banner->update($data);

            (new SpyLogger())
                ->userId(Auth::id())
                ->title("update $this->name")
                ->target($banner)
                ->permissionName("admin_panel.$this->permissionGroup.update")
                ->action('update')
                ->submit();
        });

        return $banner;
    }

    public function changeStatus(Banner $banner, mixed $status): Banner
    {
        return DB::transaction(function () use ($banner, $status) {
            Banner::withoutEvents(function () use ($banner, $status) {
                $banner->update(['status' => $status,]);
            });

            (new SpyLogger())
                ->userId(Auth::id())
                ->title("change $this->name status")
                ->description("change $this->name status to {$banner->status->getText()}")
                ->target($banner)
                ->permissionName("admin_panel.$this->permissionGroup.status")
                ->action('status')
                ->submit();

            return $banner;
        });
    }

    public function delete(Banner $banner): bool
    {
        return DB::transaction(function () use ($banner) {
            $result = $banner->delete();

            (new SpyLogger())
                ->userId(Auth::id())
                ->title("delete $this->name")
                ->target($banner)
                ->permissionName("admin_panel.$this->permissionGroup.delete")
                ->action('delete')
                ->submit();

            return $result;
        });
    }
}
