<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\Php74\Rector\Property\TypedPropertyRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Symfony\Set\SymfonySetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // get parameters
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::PHPSTAN_FOR_RECTOR_PATH, getcwd().'/phpstan.neon.dist');

    $parameters->set(Option::PATHS, [
        __DIR__.'/src',
    ]);

    $parameters->set(Option::SKIP, [
        ClassPropertyAssignToConstructorPromotionRector::class,
    ]);

    $parameters->set(
        Option::SYMFONY_CONTAINER_XML_PATH_PARAMETER,
        __DIR__.'/var/cache/dev/App_KernelDevDebugContainer.xml'
    );

    // Define what rule sets will be applied
    $containerConfigurator->import(LevelSetList::UP_TO_PHP_80);
    $containerConfigurator->import(SymfonySetList::SYMFONY_54);
    $containerConfigurator->import(SymfonySetList::SYMFONY_CODE_QUALITY);

    // get services (needed for register a single rule)
    // $services = $containerConfigurator->services();

    // register a single rule
    // $services->set(TypedPropertyRector::class);
};
