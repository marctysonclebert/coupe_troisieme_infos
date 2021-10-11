<?php
spl_autoload_register(function ($className) {
    $path = '../classes';
    $dirs = [
        '/database/',
        '/equipes/',
        '/matchs/'
    ];

    $ext = '.class.php';

    foreach ($dirs as $dir) {
        $fileName = $path . $dir . $className . $ext;
        if (file_exists($fileName)) {
            include_once($fileName);
        }
    }
});
