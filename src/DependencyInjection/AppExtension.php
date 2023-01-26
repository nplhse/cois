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
        $container->setParameter('app.enable_blog', $config['features']['enable_blog']);
        $container->setParameter('app.enable_cookie_consent', $config['features']['enable_cookie_consent']);
        $container->setParameter('app.enable_features', $config['features']['enable_features']);
        $container->setParameter('app.enable_help', $config['features']['enable_help']);
        $container->setParameter('app.enable_imprint', $config['features']['enable_imprint']);
        $container->setParameter('app.enable_registration', $config['features']['enable_registration']);
        $container->setParameter('app.enable_terms', $config['features']['enable_terms']);

        // Mailer settings
        $container->setParameter('app.mailer.from_address', $config['mailer']['from_address']);
        $container->setParameter('app.mailer.from_sender', $config['mailer']['from_sender']);
        $container->setParameter('app.mailer.admin', $config['mailer']['admin_address']);

        // Social media links
        $container->setParameter('app.social.enabled', $config['social']['enabled']);
        $container->setParameter('app.social.github', $config['social']['github']);
        $container->setParameter('app.social.mastodon', $config['social']['mastodon']);
        $container->setParameter('app.social.twitter', $config['social']['twitter']);
    }
}
