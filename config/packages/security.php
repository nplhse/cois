<?php

declare(strict_types=1);

use App\Entity\User;
use App\Security\LoginFormAuthenticator;
use Symfony\Bridge\Doctrine\Security\RememberMe\DoctrineTokenProvider;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('security', [
        'enable_authenticator_manager' => true,
        'password_hashers' => [
            PasswordAuthenticatedUserInterface::class => 'auto',
            User::class => [
                'algorithm' => 'auto', ],
        ],
        'providers' => [
            'app_user_provider' => [
                'entity' => [
                    'class' => User::class,
                    'property' => 'username',
                ],
            ],
        ],
        'firewalls' => [
            'dev' => [
                'pattern' => '^/(_(profiler|wdt)|css|images|js)/',
                'security' => false,
            ],
            'main' => [
                'lazy' => true,
                'provider' => 'app_user_provider',
                'custom_authenticator' => LoginFormAuthenticator::class,
                'logout' => [
                    'path' => 'app_logout',
                    'target' => 'app_welcome',
                ],
                'remember_me' => [
                    'secret' => '%kernel.secret%',
                    'signature_properties' => ['password'],
                    'lifetime' => 604800,
                    'path' => '/',
                    'token_provider' => DoctrineTokenProvider::class,
                ],
                'switch_user' => true,
            ],
        ],
        'access_control' => [[
            'path' => '^/admin',
            'roles' => 'ROLE_ADMIN',
        ]],
        'role_hierarchy' => [
            'ROLE_ADMIN' => ['ROLE_ALLOWED_TO_SWITCH'],
        ],
    ]);

    if ('test' === $containerConfigurator->env()) {
        $containerConfigurator->extension('security', [
            'password_hashers' => [
                PasswordAuthenticatedUserInterface::class => [
                    'algorithm' => 'auto',
                    'cost' => 4,
                    'time_cost' => 3,
                    'memory_cost' => 10,
                ],
            ],
        ]);
    }
};
