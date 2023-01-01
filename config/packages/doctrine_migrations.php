<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (\Symfony\Config\DoctrineMigrationsConfig $migrationsConfig): void {
    $migrationsConfig->migrationsPath('DoctrineMigrations', '%kernel.project_dir%/migrations');
    $migrationsConfig->enableProfiler(false);
};
