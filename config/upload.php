<?php
return [
    'storage' => [
        'disk' => env('UPLOAD_DISK', 'spaces'),
        'path' => env('UPLOAD_PATH', 'site_uploads'),
        //2 GiB max filesize
        'max_filesize' => env('UPLOAD_MAX_FILESIZE', 2048 * 1024),
        'public_url_prefix' => env('UPLOAD_PREFIX', 'https://files.zcraig.me'),
    ],
];
?>
