<?php

namespace App\Command;

use App\Entity\Import;
use App\Repository\AllocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DeleteAllocationsCommand extends Command
{
    protected static $defaultName = 'app:import:delete';
    protected static string $defaultDescription = 'Manually delete an import.';

    private EntityManagerInterface $em;

    private AllocationRepository $allocationRepository;

    public function __construct(EntityManagerInterface $entityManager, AllocationRepository $allocationRepository)
    {
        parent::__construct();

        $this->em = $entityManager;
        $this->allocationRepository = $allocationRepository;
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('id', InputArgument::OPTIONAL, 'ID of the import')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $importId = $input->getArgument('id');

        $import = $this->em->getRepository(Import::class)->findOneBy(['id' => $importId]);

        $this->allocationRepository->deleteByImport($import);

        $io->success('Successfully deleted all data from this import.');

        return Command::SUCCESS;
    }
}
