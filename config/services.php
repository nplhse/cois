<?php

declare(strict_types=1);

use App\Application\Contract\AllocationImportWriterInterface;
use App\Application\Contract\FilterInterface;
use App\Application\Contract\HandlerInterface;
use App\Application\Contract\ImportReaderInterface;
use App\Application\Contract\ImportWriterInterface;
use App\Service\FilterService;
use App\Service\Import\ImportService;
use App\Service\Import\Writer\AllocationImportWriter;
use Symfony\Bridge\Doctrine\Security\RememberMe\DoctrineTokenProvider;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set('app.locale', '%env(string:default:app.default.locale:APP_LOCALE)%');
    $parameters->set('app.supported_locales', 'en|de');

    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure()
        ->bind('$appCookieConsent', '%app.cookie_consent%')
        ->bind('$appLocale', '%app.locale%')
        ->bind('$appMailerSender', '%app.mailer.sender%')
        ->bind('$appMailerFrom', '%app.mailer.from%')
        ->bind('$appRegistration', '%app.registration%')
        ->bind('$appTerms', '%app.terms%')
        ->bind('$appTitle', '%app.title%')
        ->bind('$projectDir', '%kernel.project_dir%');

    $services->instanceof(HandlerInterface::class)
        ->public()
        ->tag('messenger.message_handler');

    $services->instanceof(FilterInterface::class)
        ->tag('app.filters');

    $services->instanceof(ImportWriterInterface::class)
        ->tag('app.import_writer');

    $services->instanceof(AllocationImportWriterInterface::class)
        ->tag('app.allocation_import_writer');

    $services->instanceof(ImportReaderInterface::class)
        ->tag('app.import_reader');

    $services->load('App\\', __DIR__.'/../src/')
        ->exclude([__DIR__.'/../src/DependencyInjection/', __DIR__.'/../src/Entity/', __DIR__.'/../src/Kernel.php', __DIR__.'/../src/Tests/']);

    $services->load('App\Controller\\', __DIR__.'/../src/Controller/')
        ->tag('controller.service_arguments');

    $services->load('App\Doctrine\\', __DIR__.'/../src/Doctrine/')
        ->tag('doctrine.orm.entity_listener');

    $services->set(DoctrineTokenProvider::class);

    $services->set(FilterService::class, FilterService::class)
        ->args([tagged_iterator(tag: 'app.filters', defaultIndexMethod: 'getParam')]);

    $services->set(ImportService::class, ImportService::class)
        ->args([service('doctrine.orm.entity_manager'), service('debug.stopwatch'), tagged_iterator(tag: 'app.import_reader', defaultIndexMethod: 'getFileType'), tagged_iterator(tag: 'app.import_writer', defaultPriorityMethod: 'getPriority')]);

    $services->set(AllocationImportWriter::class, AllocationImportWriter::class)
        ->args([tagged_iterator(tag: 'app.allocation_import_writer'), service('validator')]);
};
