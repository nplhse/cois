<?php

declare(strict_types=1);

namespace App\Controller\Data\Export;

use App\Query\Export\DGINA23ExportQuery;
use League\Csv\Writer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

class ExportDGINA23UsageByTimesController extends AbstractController
{
    public function __construct(
        private DGINA23ExportQuery $query
    ) {
    }

    #[Route('/export/dgina23/usageByTimes', name: 'app_export_dgina23_times')]
    public function __invoke(): void
    {
        $data = [];

        $data['total'] = $this->query->findAllocationsByHour()->getResults();
        $data['int'] = $this->query->findAllocationsByHour()->filterBySpeciality('Innere Medizin')->getResults();
        $data['int_cathlab'] = $this->query->findAllocationsByHour()->filterBySpeciality('Innere Medizin')->filterByProperty('cathlab')->getResults();
        $data['trauma'] = $this->query->findAllocationsByHour()->filterBySpeciality('Unfallchirurgie')->getResults();
        $data['trauma_resus'] = $this->query->findAllocationsByHour()->filterBySpeciality('Unfallchirurgie')->filterByProperty('resus')->getResults();
        $data['neuro'] = $this->query->findAllocationsByHour()->filterBySpeciality('Neurologie')->getResults();
        $data['neuro_resus'] = $this->query->findAllocationsByHour()->filterBySpeciality('Neurologie')->filterByProperty('resus')->getResults();
        $data['peds'] = $this->query->findAllocationsByHour()->filterBySpeciality('Kinder- und Jugendmedizin')->getResults();
        $data['urology'] = $this->query->findAllocationsByHour()->filterBySpeciality('Urologie')->getResults();

        foreach ($data as $key => $row) {
            $tmp = [];

            for ($i = 0; $i <= 23; ++$i) {
                $tmp[$i] = 0;
            }

            for ($i = 0, $iMax = count($row); $i < $iMax; ++$i) {
                $tmp[$row[$i]['hour']] = $row[$i]['value'];
            }

            array_unshift($tmp, $key);
            $data[$key] = $tmp;
        }

        array_unshift($data, ['caption', 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23]);

        if (!ini_get('auto_detect_line_endings')) {
            ini_set('auto_detect_line_endings', '1');
        }

        $csv = Writer::createFromPath('php://temp', 'r+');
        $csv->setDelimiter(';');
        $csv->setOutputBOM(Writer::BOM_UTF8);

        $csv->insertAll($data);

        $flushThreshold = 500;
        $contentCallback = function () use ($csv, $flushThreshold): void {
            foreach ($csv->chunk(1024) as $offset => $chunk) {
                echo $chunk;

                if (0 === $offset % $flushThreshold) {
                    flush();
                }
            }
        };

        $response = new StreamedResponse();
        $response->headers->set('Content-Encoding', 'none');
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');

        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'dgina23-usage-by-times-'.date('d-m-Y-h-i').'.csv'
        );

        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Description', 'File Transfer');
        $response->setCallback($contentCallback);
        $response->send();
    }
}
