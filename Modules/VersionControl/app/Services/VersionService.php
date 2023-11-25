<?php

namespace Modules\VersionControl\app\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Spy\app\Utilities\SpyLogger;
use Modules\VersionControl\app\Models\Version;

class VersionService
{
    protected string $permissionGroup = 'version';
    protected string $name = 'version';

    public function getAlias(): string
    {
        return __('versioncontrol::aliases.name.version');
    }

    public function create(array $data): Version
    {
        return DB::transaction(function () use ($data) {
            $version = Version::create($data);

            (new SpyLogger())
                ->userId(Auth::id())
                ->title("create $this->name")
                ->target($version)
                ->permissionName("admin_panel.$this->permissionGroup.create")
                ->action('create')
                ->submit();

            return $version;
        });
    }

    public function update(Version $version, array $data): Version
    {
        DB::transaction(function () use ($version, $data) {
            $version->update($data);

            (new SpyLogger())
                ->userId(Auth::id())
                ->title("update $this->name")
                ->target($version)
                ->permissionName("admin_panel.$this->permissionGroup.update")
                ->action('update')
                ->submit();
        });

        return $version;
    }

    public function delete(Version $version): bool
    {
        return DB::transaction(function () use ($version) {
            $result = $version->delete();

            (new SpyLogger())
                ->userId(Auth::id())
                ->title("delete $this->name")
                ->target($version)
                ->permissionName("admin_panel.$this->permissionGroup.delete")
                ->action('delete')
                ->submit();

            return $result;
        });
    }

    public function getLatestVersionNumber(): int
    {
        return Version::orderBy('version_number')->first()?->version_number ?? 0;
    }
}
