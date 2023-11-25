<?php

return [
    'name' => 'Gallery',

    'setting_cache_key' => 'gallery_setting',

    'base_path' => 'gallery/',

    'upload_image_throw_ftp' => env('UPLOAD_IMAGE_THROW_FTP', false),
    'upload_Video_throw_ftp' => env('UPLOAD_VIDEO_THROW_FTP', false),
    'image_gallery_occupation_status' => env('IMAGE_GALLERY_OCCUPATION_STATUS', true),
    'video_gallery_occupation_status' => env('VIDEO_GALLERY_OCCUPATION_STATUS', true),

    'image_generate_patterns' => [
//        ['width' => 200, 'height' => 176, 'extension' => 'png', 'quality' => 90, 'observe_aspect_ratio' => false,],
    ],
];
