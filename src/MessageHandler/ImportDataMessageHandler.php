<?php

namespace App\MessageHandler;

use App\Message\ImportDataMessage;
use App\Service\ImportService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ImportDataMessageHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    private ImportService $service;

    public function __construct(EntityManagerInterface $entityManager, ImportService $service)
    {
        $this->em = $entityManager;
        $this->service = $service;
    }

    public function __invoke(ImportDataMessage $message): void
    {
        // 1. Prepare
        $import = $message->getImport();
        $hospital = $message->getHospital();

        if ($message->getCli()) {
            $path = 'var/storage/import/'.$import->getPath();
        } else {
            $path = '../var/storage/import/'.$import->getPath();
        }

        if (!file_exists($path)) {
            throw new FileNotFoundException($path);
        }

        $this->service->importFromCSV($path);

        switch ($import->getContents()) {
            case 'allocation':
                $this->service->processToAllocation($import, $hospital);
        }
    }
}
