<?php

namespace App\MessageHandler;

use App\Entity\Allocation;
use App\Message\ImportDataMessage;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ImportDataMessageHandler implements MessageHandlerInterface
{
    private EntityManager $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function __invoke(ImportDataMessage $message): void
    {
        $import = $message->getImport();
        $hospital = $message->getHospital();

        $csv = Reader::createFromPath('../var/storage/import/'.$import->getPath(), 'r');
        $csv->setHeaderOffset(0);
        $csv->setDelimiter(';');
        $csv->setEnclosure('"');
        $csv->includeEmptyRecords();

        $header = $csv->getHeader();
        dump($header);

        $result = Info::getDelimterStats($csv, [' ', '|'], 10);

        $results = $csv->getRecords();
        dump($results);
    }
}
