<?php

namespace Modules\VersionControl\database\seeders;

use Illuminate\Database\Seeder;
use Modules\VersionControl\app\Models\Platform;
use Modules\VersionControl\app\Models\Version;
use Modules\VersionControl\app\Services\VersionService;

class VersionControlDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Platform::withoutEvents(function () {
            $platforms = Platform::factory()->count(5)->create();

            foreach ($platforms as $platform) {
                Version::factory()->count(1)->create([
                    'platform_id' => $platform->id,
                    'version_number' => (new VersionService())->getLatestVersionNumber(),
                ]);
            }
        });
    }
}
