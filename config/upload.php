<?php
return [
    'storage' => [
        'path' => env('uploads.storage_path', 'user_uploads'),
        'max_filesize' => env('uploads.max_filesize', '2048')
    ],
];
?>
