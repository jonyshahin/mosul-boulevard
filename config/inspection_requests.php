<?php

return [
    'max_file_size_mb' => env('INSPECTION_MAX_FILE_MB', 50),

    'signed_url_ttl_minutes' => env('INSPECTION_SIGNED_URL_TTL', 30),

    'reply_edit_window_minutes' => env('INSPECTION_REPLY_EDIT_WINDOW', 15),

    'allowed_image_mimes' => [
        'image/jpeg',
        'image/png',
        'image/webp',
    ],

    'allowed_video_mimes' => [
        'video/mp4',
        'video/quicktime',
        'video/webm',
    ],

    'notification_queue' => env('INSPECTION_NOTIFICATION_QUEUE', 'notifications'),

    'overdue_check_time' => env('INSPECTION_OVERDUE_TIME', '08:00'),

    'overdue_timezone' => env('INSPECTION_TIMEZONE', 'Asia/Baghdad'),

    'default_locale' => env('APP_LOCALE', 'en'),
];
