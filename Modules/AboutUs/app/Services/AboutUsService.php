<?php

namespace Modules\AboutUs\app\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\AboutUs\app\Models\AboutUs;
use Modules\Spy\app\Utilities\SpyLogger;

class AboutUsService
{
    protected string $permissionGroup = 'about_us';
    protected string $name = 'about us';
    protected string $cacheKey;

    public function __construct()
    {
        $this->cacheKey = config('aboutus.cache_key');
    }

    public function getAlias(): string
    {
        return __('aboutus::aliases.name.about-us');
    }

    public function update(AboutUs $aboutUs, array $data): AboutUs
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
        DB::table('about_us')->insert($this->getInitialData());

        Cache::put($this->cacheKey, AboutUs::firstOrFail());
    }
}
