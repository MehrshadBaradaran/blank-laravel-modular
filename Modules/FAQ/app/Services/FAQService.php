<?php

namespace Modules\FAQ\app\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\FAQ\app\Models\FAQ;
use Modules\Spy\app\Utilities\SpyLogger;

class FAQService
{
    protected string $permissionGroup = 'faq';
    protected string $name;

    public function __construct()
    {
        $this->name = __('faq::aliases.name.faq', locale: 'en');
    }

    public function getAlias(): string
    {
        return __('faq::aliases.name.faq');
    }

    public function create(array $data): FAQ
    {
        return DB::transaction(function () use ($data) {
            $faq = FAQ::create($data);

            (new SpyLogger())
                ->userId(Auth::id())
                ->title("create $this->name")
                ->target($faq)
                ->permissionName("admin_panel.$this->permissionGroup.create")
                ->action('create')
                ->submit();

            return $faq;
        });
    }

    public function update(FAQ $faq, array $data): FAQ
    {
        DB::transaction(function () use ($faq, $data) {
            $faq->update($data);

            (new SpyLogger())
                ->userId(Auth::id())
                ->title("update $this->name")
                ->target($faq)
                ->permissionName("admin_panel.$this->permissionGroup.update")
                ->action('update')
                ->submit();
        });

        return $faq;
    }

    public function delete(FAQ $faq): bool
    {
        return DB::transaction(function () use ($faq) {
            $result = $faq->delete();

            (new SpyLogger())
                ->userId(Auth::id())
                ->title("delete $this->name")
                ->target($faq)
                ->permissionName("admin_panel.$this->permissionGroup.delete")
                ->action('delete')
                ->submit();

            return $result;
        });
    }

    public function changeStatus(FAQ $faq, mixed $status): FAQ
    {
        return DB::transaction(function () use ($faq, $status) {
            $faq->update(['status' => $status,]);

            (new SpyLogger())
                ->userId(Auth::id())
                ->title("change $this->name status")
                ->description("change $this->name status to {$faq->status->getText()}")
                ->target($faq)
                ->permissionName("admin_panel.$this->permissionGroup.change-status")
                ->action('status')
                ->submit();

            return $faq;
        });
    }

    public function sort(array $faqIds): void
    {
        DB::transaction(function () use ($faqIds) {
            $counter = 0;

            foreach ($faqIds as $id) {
                $faq = FAQ::findOrFail($id);

                $faq->update([
                    'sort_index' => $counter,
                ]);

                $counter++;
            }

            (new SpyLogger())
                ->userId(Auth::id())
                ->title("sort of $this->name")
                ->description("change the order of $this->name")
                ->permissionName("admin_panel.$this->permissionGroup.sort")
                ->submit();
        });
    }
}
