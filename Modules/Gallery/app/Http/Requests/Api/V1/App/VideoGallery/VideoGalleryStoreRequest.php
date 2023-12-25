<?php

namespace Modules\Gallery\app\Http\Requests\Api\V1\App\VideoGallery;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Modules\Gallery\app\Enums\ImageGallerySectionEnum;
use Modules\Gallery\app\Services\VideoService;

class VideoGalleryStoreRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function passedValidation(): void
    {
        $data = (new VideoService())->handle($this->file, $this->section);

        $this->merge([
            'folder' => $data->getFolder(),
            'generated_files' => $data->getFiles(),

            'size' => $data->getSize(),
            'duration' => $data->getDuration(),

            'user_id' => \Auth::id(),
        ]);
    }

    public function rules(): array
    {
        return [
            'section' => [
                'required', 'string',
                new Enum(ImageGallerySectionEnum::class),
            ],

            'file' => [
                'required', 'mimes:mp4,webm,mkv,m4v,flv,gif',
                'max:' . \Cache::get('gallerySetting')->max_video_upload_size,
            ],
        ];
    }

    public function getSafeData(): array
    {
        return [
            'user_id' => $this->user_id,

            'section' => $this->section,

            'folder' => $this->folder,
            'files' => $this->generated_files,

            'duration' => $this->duration,
            'size' => $this->size,

            'occupied' => false,
        ];
    }
}
