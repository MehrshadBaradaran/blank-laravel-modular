<?php

namespace Modules\Setting\app\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Setting\app\Models\Setting;
use Modules\Spy\app\Utilities\SpyLogger;

class SettingService
{
    protected string $permissionGroup = 'setting';
    protected string $name = 'setting';
    protected string $cacheKey;

    public function __construct()
    {
        $this->cacheKey = config('setting.cache_key');
    }

    public function getAlias(): string
    {
        return __('setting::aliases.name.setting');
    }

    public function update(Setting $aboutUs, array $data): Setting
    {
        DB::transaction(function () use ($data, $aboutUs) {

            $aboutUs->update($data);

            Cache::forget($this->cacheKey);
            Cache::put($this->cacheKey, $aboutUs);

            (new SpyLogger())
                ->userId(Auth::id())
                ->title("update $this->name")
                ->target($aboutUs)
                ->permissionName("admin_panel.$this->permissionGroup.create")
                ->action('create')
                ->submit();
        });

        return $aboutUs;
    }

    public function getInitialData(): array
    {
        return [
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function initialize()
    {
        DB::table('settings')->insert($this->getInitialData());

        Cache::put($this->cacheKey, Setting::firstOrFail());
    }
}
