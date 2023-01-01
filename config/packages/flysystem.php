<?php

declare(strict_types=1);

return static function (Symfony\Config\FlysystemConfig $flysystemConfig): void {
    $flysystemConfig->storage('defaultStorage')
        ->adapter('local')
        ->options([
            'directory' => '%kernel.project_dir%/var/storage/import',
        ]);
};
