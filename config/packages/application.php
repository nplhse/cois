<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('app', [
        'title' => 'Collaborative IVENA statistics',
        'default_locale' => 'en',
        'features' => [
            'enable_blog' => true,
            'enable_cookie_consent' => true,
            'enable_features' => true,
            'enable_help' => false,
            'enable_imprint' => true,
            'enable_registration' => true,
            'enable_terms' => true,
        ],
        'mailer' => [
            'from_address' => 'noreply@cois.dev',
            'from_sender' => 'Collaborative IVENA Statistics',
            'admin_address' => 'admin@cois.dev',
        ],
        'social' => [
            'enabled' => true,
            'github' => 'nplhse/cois',
            'twitter' => 'VerForschung',
        ],
    ]);
};
