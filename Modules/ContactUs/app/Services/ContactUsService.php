<?php

namespace Modules\ContactUs\app\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\ContactUs\app\Models\ContactUs;
use Modules\Spy\app\Utilities\SpyLogger;

class ContactUsService
{
    protected string $permissionGroup = 'contact_us';
    protected string $name = 'contact us';
    protected string $cacheKey;

    public function __construct()
    {
        $this->cacheKey = config('contactus.cache_key');
    }

    public function getAlias(): string
    {
        return __('contactus::aliases.name.contact-us');
    }

    public function update(ContactUs $contactUs, array $data): ContactUs
    {
        DB::transaction(function () use ($data, $contactUs) {

            $contactUs->update($data);

            Cache::forget($this->cacheKey);
            Cache::put($this->cacheKey, $contactUs);

            (new SpyLogger())
                ->userId(Auth::id())
                ->title("update $this->name")
                ->target($contactUs)
                ->permissionName("admin_panel.$this->permissionGroup.create")
                ->action('create')
                ->submit();
        });

        return $contactUs;
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
        DB::table('contact_us')->insert($this->getInitialData());

        Cache::put($this->cacheKey, ContactUs::firstOrFail());
    }
}
