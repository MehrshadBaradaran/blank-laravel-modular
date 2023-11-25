<?php

namespace Modules\Notification\app\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Notification\app\Models\Notification;
use Modules\Spy\app\Utilities\SpyLogger;

class NotificationService
{
    protected string $permissionGroup = 'notification';
    protected string $name = 'notification';

    public function getAlias(): string
    {
        return __('notification::aliases.name.notification');
    }

    public function create(array $data, array $users): Notification
    {
        return DB::transaction(function () use ($data, $users) {
            $notification = Notification::create($data);

            $notification->users()->attach($users);

            (new SpyLogger())
                ->userId(Auth::id())
                ->title("create $this->name")
                ->target($notification)
                ->permissionName("admin_panel.$this->permissionGroup.create")
                ->action('create')
                ->submit();

            return $notification;
        });
    }

    public function update(Notification $notification, array $data, array $users): Notification
    {
        DB::transaction(function () use ($notification, $data, $users) {
            $notification->update($data);

            $notification->users()->sync($users);

            (new SpyLogger())
                ->userId(Auth::id())
                ->title("update $this->name")
                ->target($notification)
                ->permissionName("admin_panel.$this->permissionGroup.update")
                ->action('update')
                ->submit();
        });

        return $notification;
    }

    public function delete(Notification $notification): bool
    {
        return DB::transaction(function () use ($notification) {

            $result = $notification->delete();

            (new SpyLogger())
                ->userId(Auth::id())
                ->title("delete $this->name")
                ->target($notification)
                ->permissionName("admin_panel.$this->permissionGroup.delete")
                ->action('delete')
                ->submit();

            return $result;
        });
    }

    public function changeStatus(Notification $notification, mixed $status): Notification
    {
        return DB::transaction(function () use ($notification, $status) {

            $notification->update(['status' => $status,]);

            (new SpyLogger())
                ->userId(Auth::id())
                ->title("change $this->name status")
                ->description("change $this->name status to {$notification->status->getText()}")
                ->target($notification)
                ->permissionName("admin_panel.$this->permissionGroup.change-status")
                ->action('status')
                ->submit();

            return $notification;
        });
    }
}
