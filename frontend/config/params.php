<?php
return [
    'adminEmail' => 'admin@example.com',

    'maxFileSize'    => 1024 * 1024 * 2, // 2 megabites
    'storagePath'    => '@frontend/web/uploads/',
    'storageUri'     => '/uploads/',
    // Настройки могут быть вложенными
    'profilePicture' => [
        'maxWidth'  => 1024,
        'maxHeight' => 768,
    ],
    'feedPostsLimit' => 200
];
