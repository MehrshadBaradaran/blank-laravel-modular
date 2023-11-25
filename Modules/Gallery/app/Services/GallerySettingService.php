<?php

namespace Modules\Gallery\app\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Gallery\app\Models\GallerySetting;
use Modules\Spy\app\Utilities\SpyLogger;

class GallerySettingService
{
    protected string $permissionGroup = 'gallery_setting';
    protected string $name = 'gallery setting';
    protected string $cacheKey;

    public function __construct()
    {
        $this->cacheKey = config('gallery.setting_cache_key');
    }

    public function getAlias(): string
    {
        return __('gallery::aliases.name.gallery-setting');
    }

    public function update(GallerySetting $gallerySetting, array $data): GallerySetting
    {
        DB::transaction(function () use ($data, $gallerySetting) {

            $gallerySetting->update($data);

            Cache::forget($this->cacheKey);
            Cache::put($this->cacheKey, $gallerySetting);

            (new SpyLogger())
                ->userId(Auth::id())
                ->title("update $this->name")
                ->target($gallerySetting)
                ->permissionName("admin_panel.$this->permissionGroup.update")
                ->action('update')
                ->submit();

        });

        return $gallerySetting;
    }

    public function getInitialData(): array
    {
        $imageGeneratePatterns = config('gallery.image_generate_patterns');

        return [
            'max_image_upload_size' => 1024 * 2,
            'max_video_upload_size' => 1024 * 1000,

            'image_generate_patterns' => !empty($imageGeneratePatterns) ? json_encode($imageGeneratePatterns) : null,

            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function initialize()
    {
        DB::table('gallery_settings')->insert($this->getInitialData());

        Cache::put($this->cacheKey, GallerySetting::firstOrFail());
    }
}
