<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('twig', [
        'default_path' => '%kernel.project_dir%/templates',
        'form_themes' => ['bootstrap_5_layout.html.twig'],
        'date' => [
            'format' => 'd.m.Y',
            'interval_format' => '%%d days',
            'timezone' => 'Europe/Berlin',
        ],
        'globals' => [
            'app_cookie_consent' => '%app.cookie_consent%',
            'app_registration' => '%app.registration%',
            'app_terms' => '%app.terms%',
            'app_title' => '%app.title%',
        ],
    ]);

    if ('test' === $containerConfigurator->env()) {
        $containerConfigurator->extension('twig', [
            'strict_variables' => true,
        ]);
    }
};
