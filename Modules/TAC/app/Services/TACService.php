<?php

namespace Modules\TAC\app\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\TAC\app\Models\TAC;
use Modules\Spy\app\Utilities\SpyLogger;

class TACService
{
    protected string $permissionGroup = 'tac';
    protected string $name;
    protected string $cacheKey;

    public function __construct()
    {
        $this->name = __('tac::aliases.name.tac', locale: 'en');
        $this->cacheKey = config('tac.cache_key');
    }

    public function getAlias(): string
    {
        return __('tac::aliases.name.tac');
    }

    public function update(TAC $aboutUs, array $data): TAC
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
        DB::table('terms_and_conditions')->insert($this->getInitialData());

        Cache::put($this->cacheKey, TAC::firstOrFail());
    }
}
