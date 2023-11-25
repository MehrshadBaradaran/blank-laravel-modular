<?php

namespace Modules\Gallery\app\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Modules\Gallery\app\Models\GallerySetting;

class ImageService
{
    protected array|null $ImageGeneratePatterns;
    protected array $files;
    protected string $folder;
    protected string $extension;
    protected int $width;
    protected int $height;
    protected int $size;

    public function __construct()
    {
        $this->ImageGeneratePatterns = GallerySetting::firstOrFail()->image_generate_patterns;
    }

    protected function handleMasterImage(UploadedFile $image, string $section): static
    {
        $extension = $image->extension();

        $imgName = "master_q100.$extension";
        $path = config('gallery.base_path') . "images/$section/" . Str::uuid();

        Storage::disk('public')->putfileAs($path, $image, $imgName);

        $this->folder = $path;

        $this->extension = $extension;

        $this->files['master'] = "$this->folder/$imgName";

        $this->size = $image->getSize();

        [$this->width, $this->height] = getimagesize($image);

        return $this;
    }

    public function handle(UploadedFile $image, string $section): static
    {
        $this->handleMasterImage($image, $section);

        if ($this->ImageGeneratePatterns) {
            foreach ($this->ImageGeneratePatterns as $data) {
                $img = Image::make($image);

                $img->resize($data['width'], $data['height'], function ($constraint) use ($data) {
                    if ($data['observe_aspect_ratio']) {
                        $constraint->aspectRatio();
                    }
                });

                $imgName = "w{$data['width']}_h{$data['height']}_q{$data['quality']}.{$data['extension']}";

                $img->encode($data['extension'], $data['quality']);

                Storage::disk('public')->put("$this->folder/$imgName", $img->__toString());

                $this->files["{$data['width']}_{$data['height']}"] = "$this->folder/$imgName";
            }
        }

        return $this;
    }

    public function getFolder(): string
    {
        return $this->folder;
    }

    public function getFiles(): array
    {
        return $this->files;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }
}
