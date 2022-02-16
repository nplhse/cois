<?php

namespace App\Command;

use App\Entity\Import;
use App\Repository\ImportRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:import:update',
    description: 'Update metadata of imports',
)]
class UpdateImportCommand extends Command
{
    private ImportRepository $importRepository;

    public function __construct(ImportRepository $importRepository)
    {
        parent::__construct();

        $this->importRepository = $importRepository;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $imports = $this->importRepository->findAll();

        foreach ($imports as $import) {
            $import->setName($import->getCaption());
            $import->setType(Import::TYPE_ALLOCATION);
            $import->setFileMimeType(Import::MIME_PLAIN);
            $import->setFileSize($import->getSize());
            $import->setFileExtension(Import::EXT_CSV);
            $import->setFilePath($import->getPath());
        }

        $this->importRepository->save();

        $io->success('Updated metadata of all imports');

        return Command::SUCCESS;
    }
}
