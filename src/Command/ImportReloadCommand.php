<?php

namespace App\Command;

use App\Domain\Command\Import\ImportDataCommand;
use App\Repository\AllocationRepository;
use App\Repository\ImportRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:import:reload',
    description: 'Reload all Allocations for a given Import',
)]
class ImportReloadCommand extends Command
{
    private MessageBusInterface $messageBus;

    private AllocationRepository $allocationRepository;

    private ImportRepository $importRepository;

    public function __construct(MessageBusInterface $messageBus, AllocationRepository $allocationRepository, ImportRepository $importRepository, string $name = null)
    {
        parent::__construct($name);

        $this->messageBus = $messageBus;
        $this->allocationRepository = $allocationRepository;
        $this->importRepository = $importRepository;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('id', InputArgument::REQUIRED, 'Import Id')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $importId = $input->getArgument('id');

        try {
            $import = $this->importRepository->findOneBy(['id' => $importId]);
            $this->allocationRepository->deleteByImport($import);
            $this->messageBus->dispatch(new ImportDataCommand($importId));
        } catch (HandlerFailedException $e) {
            $io->warning(sprintf('Something went wrong! Failed to reload import %d: %s.', $importId, $e->getMessage()));

            return Command::FAILURE;
        }

        $io->success('Import has been refreshed.');

        return Command::SUCCESS;
    }
}