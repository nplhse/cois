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
            'app_title' => '%app.title%',
            'app_enable_blog' => '%app.enable_blog%',
            'app_enable_cookie_consent' => '%app.enable_cookie_consent%',
            'app_enable_features' => '%app.enable_features%',
            'app_enable_help' => '%app.enable_help%',
            'app_enable_imprint' => '%app.enable_imprint%',
            'app_enable_registration' => '%app.enable_registration%',
            'app_enable_terms' => '%app.enable_terms%',
            'app_social' => '%app.social.enabled%',
            'app_social_github' => '%app.social.github%',
            'app_social_mastodon' => '%app.social.mastodon%',
            'app_social_twitter' => '%app.social.twitter%',
        ],
    ]);

    if ('test' === $containerConfigurator->env()) {
        $containerConfigurator->extension('twig', [
            'strict_variables' => true,
        ]);
    }
};
