<?php

declare(strict_types=1);

namespace App\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class AppExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new AppConfiguration();
        $config = $this->processConfiguration($configuration, $configs);

        // General settings
        $container->setParameter('app.title', $config['title']);
        $container->setParameter('app.default.locale', $config['default_locale']);

        // Feature switches
        $container->setParameter('app.registration', $config['features']['enable_registration']);
        $container->setParameter('app.terms', $config['features']['enable_terms']);
        $container->setParameter('app.cookie_consent', $config['features']['enable_cookie_consent']);

        // Mailer settings
        $container->setParameter('app.mailer.from_address', $config['mailer']['from_address']);
        $container->setParameter('app.mailer.from_sender', $config['mailer']['from_sender']);
        $container->setParameter('app.mailer.admin', $config['mailer']['admin_address']);
    }
}
