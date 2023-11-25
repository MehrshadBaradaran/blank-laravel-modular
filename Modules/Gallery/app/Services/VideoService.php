<?php

namespace Modules\Gallery\app\Services;

use getID3;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VideoService
{
    protected array $files;
    protected string $folder;
    protected string $extension;
    protected int $size;
    protected int $duration;

    protected function handleMasterImage(UploadedFile $video, string $section): static
    {
        $extension = $video->extension();

        $imgName = "master_q100.$extension";
        $path = config('gallery.base_path') . "videos/$section/" . Str::uuid();
        $analyzedVideoData = (new getID3())->analyze($video);

        Storage::disk('public')->putfileAs($path, $video, $imgName);

        $this->folder = $path;
        $this->extension = $extension;
        $this->size = $video->getSize();
        $this->duration = (int)floor($analyzedVideoData['playtime_seconds']);
        $this->files['master'] = "$this->folder/$imgName";

        return $this;
    }

    public function handle(UploadedFile $video, string $section): static
    {
        $this->handleMasterImage($video, $section);

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

    public function getSize(): int
    {
        return $this->size;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }
}
