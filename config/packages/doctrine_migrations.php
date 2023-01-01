<?php

declare(strict_types=1);

return static function (Symfony\Config\DoctrineMigrationsConfig $migrationsConfig): void {
    $migrationsConfig->migrationsPath('DoctrineMigrations', '%kernel.project_dir%/migrations');
    $migrationsConfig->enableProfiler(false);
};
