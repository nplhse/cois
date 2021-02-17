<?php

namespace App\MessageHandler;

use App\Message\ImportDataMessage;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\CharsetConverter;
use League\Csv\Reader;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use \ForceUTF8\Encoding;

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
        $path = '../var/storage/import/'.$import->getPath();
        $hospital = $message->getHospital();

        $str = $this->arrayFromCSV($path, true);

        /*
        //$csv = Reader::createFromFileObject(new \SplFileObject($path));
        $csv = Reader::createFromPath('../var/storage/import/'.$import->getPath(), 'r');
        $csv->setHeaderOffset(0);
        $csv->setDelimiter(';');
        $csv->setEnclosure('"');
        $csv->includeEmptyRecords();

        $encoder = (new CharsetConverter())->inputEncoding('iso-8859-15');
        $records = $encoder->convert($csv);
        dump($records);

        $header = $csv->getHeader();

        $results = $csv->getRecords($utfEncoded);
        dump($results);*/
    }

    private function arrayFromCSV($file, $hasFieldNames = false, $delimiter = ';', $enclosure = '"')
    {
        $result = [];
        $size = filesize($file) + 1;
        $file = fopen($file, 'r');

        //TO DO: There must be a better way of finding out the size of the longest row... until then
        if ($hasFieldNames) {
            $keys = fgetcsv($file, $size, $delimiter, $enclosure);
        }

        while ($row = fgetcsv($file, $size, $delimiter, $enclosure)) {
            $n = count($row);
            $res = [];
            for ($i = 0; $i < $n; ++$i) {
                $idx = ($hasFieldNames) ? $keys[$i] : $i;
                dump($keys[$i]);
                $res[$idx] = $row[$i];
            }
            $result[] = $res;
        }
        fclose($file);

        return $result;
    }
}
