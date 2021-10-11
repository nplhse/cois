<?php

namespace App\Command;

use App\Entity\Import;
use App\Message\ImportDataMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:import',
    description: 'Add a short description for your command',
)]
class ImportCommand extends Command
{
    private EntityManagerInterface $em;

    private MessageBusInterface $messageBus;

    public function __construct(EntityManagerInterface $entityManager, MessageBusInterface $messageBus)
    {
        parent::__construct();

        $this->em = $entityManager;
        $this->messageBus = $messageBus;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('id', InputArgument::OPTIONAL, 'ID of the import');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $importId = $input->getArgument('id');

        $import = $this->em->getRepository(Import::class)->findOneBy(['id' => $importId]);

        $user = $import->getUser();
        $hospital = $user->getHospital();

        if ($importId) {
            $io->note(sprintf('The related input is: %s', $import->getCaption()));
            $io->note(sprintf('The related user is: %s', $user->getUsername()));
            $io->note(sprintf('The related hospital is: %s', $hospital->getName()));
        }

        $command = new ImportDataMessage($import, $hospital, true);

        $this->messageBus->dispatch($command);

        $io->success('Successfully imported all data.');

        return Command::SUCCESS;
    }
}
