<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routingConfigurator): void {
    $routingConfigurator->import('../../src/App/Controller/', 'annotation');
    $routingConfigurator->import('../../src/App/Kernel.php', 'annotation');
};
