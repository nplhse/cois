<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('zenstruck_foundry', [
        // Whether to auto-refresh proxies by default (https://github.com/zenstruck/foundry#auto-refresh)
        'auto_refresh_proxies' => false,
    ]);

    if ('test' === $containerConfigurator->env()) {
        $containerConfigurator->extension('zenstruck_foundry', [
            'global_state' => [
                'App\Tests\Story\GlobalStory',
            ],
        ]);
    }
};
