<?php

declare(strict_types=1);

use Symfony\Config\FlysystemConfig;

return static function (FlysystemConfig $flysystemConfig): void {
    $flysystemConfig->storage('defaultStorage')
        ->adapter('local')
        ->options([
            'directory' => '%kernel.project_dir%/var/storage/import',
        ]);
};
