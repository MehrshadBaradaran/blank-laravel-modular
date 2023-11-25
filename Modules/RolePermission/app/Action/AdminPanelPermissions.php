<?php

namespace Modules\RolePermission\app\Action;


use Modules\AboutUs\app\Models\AboutUs;
use Modules\Banner\app\Models\Banner;
use Modules\ContactUs\app\Models\ContactUs;
use Modules\Gallery\app\Models\GallerySetting;
use Modules\Gallery\app\Models\ImageGallery;
use Modules\Gallery\app\Models\VideoGallery;
use Modules\Notification\app\Models\Notification;
use Modules\RolePermission\app\Models\Role;
use Modules\RolePermission\app\Utilities\PermissionGenerator;
use Modules\Setting\app\Models\Setting;
use Modules\Spy\app\Models\Spy;
use Modules\User\app\Models\User;
use Modules\VersionControl\app\Models\Platform;
use Modules\VersionControl\app\Models\Version;

class AdminPanelPermissions
{
    public static function get(): array
    {
        $permissionGenerator = new PermissionGenerator('admin_panel');

        // Role
        $permissionGenerator->new(Role::class)->all()->attach();

        // User
        $permissionGenerator->new(User::class)->all();

        // Notification
        $permissionGenerator->new(Notification::class)->all();

        // Spy
        $permissionGenerator->new(Spy::class)->view();

        // ImageGallery
        $permissionGenerator->new(ImageGallery::class)->view()->create()->delete();

        // VideoGallery
        $permissionGenerator->new(VideoGallery::class)->view()->create()->delete();

        // Banner
        $permissionGenerator->new(Banner::class)->all();

        // FrequentlyAskedQuestions
        $permissionGenerator->new('faq')->all()->sort();

        // TermsAndConditions
        $permissionGenerator->new('tac')->view()->update();

        // AboutUs
        $permissionGenerator->new(AboutUs::class)->view()->update();

        // ContactUs
        $permissionGenerator->new(ContactUs::class)->view()->update();

        // Platform
        $permissionGenerator->new(Platform::class, false)->all();

        // Version
        $permissionGenerator->new(Version::class, false)->all()->except('change-status');

        // GallerySetting
        $permissionGenerator->new(GallerySetting::class, false)->view()->update();

        // Setting
        $permissionGenerator->new(Setting::class)->view()->update();


        return $permissionGenerator->get();
    }
}
