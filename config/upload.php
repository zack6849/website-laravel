<?php
return [
    'storage' => [
        'path' => env('uploads.storage_path', 'user_uploads'),
        //2 GiB max filesize
        'max_filesize' => env('uploads.max_filesize', 2048 * 1024)
    ],
];
?>
