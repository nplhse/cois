<?php

declare(strict_types=1);

namespace App\Twig\Components;

use Domain\Command\Export\ExportTracerByQuarterCommand;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent('download_export')]
final class DownloadExportComponent
{
    use DefaultActionTrait;

    #[LiveProp]
    public string $target = 'dgina_tracer_by_quarter';

    #[LiveProp]
    public ?string $status = null;

    private ?object $activeExport = null;

    public function __construct(
        #[TaggedIterator('app.export')]
        private iterable $exports,
        private MessageBusInterface $messageBus,
    ) {
        $this->activeExport = $this->selectExport();

        if ($this->fileExists()) {
            $this->status = 'available';
        } else {
            $this->status = 'unavailable';
        }
    }

    public function fileExists(): bool
    {
        return $this->activeExport->fileExists($this->target);
    }

    public function targetName(): string
    {
        return $this->target;
    }

    public function getStatus(): string
    {
        if ('rendering' === $this->status) {
            return $this->status;
        }

        if ($this->fileExists()) {
            return 'available';
        }

        return 'unavailable';
    }

    #[LiveAction]
    public function createFile(): void
    {
        $this->status = 'rendering';

        $command = new ExportTracerByQuarterCommand();
        $this->messageBus->dispatch($command);
    }

    private function selectExport(): object
    {
        foreach ($this->exports as $export) {
            if ($export->getName() === $this->target) {
                return $export;
            }
        }

        throw new \Exception('No Export was selected');
    }
}
