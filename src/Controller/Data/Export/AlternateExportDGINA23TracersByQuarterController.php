<?php

declare(strict_types=1);

namespace App\Controller\Data\Export;

use App\Query\Export\AlternateQuery;
use League\Csv\Writer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

class AlternateExportDGINA23TracersByQuarterController extends AbstractController
{
    public function __construct(
        private AlternateQuery $query
    ) {
    }

    #[Route('/export/dgina23/tracerByQuarter_alt', name: 'app_export_dgina23_tracer_alt')]
    public function __invoke(): void
    {
        $data = [];

        $data['total'] = $this->query->sumTotalAllocationsByQuarter();
        $data['cpr'] = $this->query->sumAllocationsByQuarter('cpr');
        $data['pulmonary_embolism'] = $this->query->sumAllocationsByQuarter('pulmonary_embolism');
        $data['acs_stemi'] = $this->query->sumAllocationsByQuarter('acs_stemi');
        $data['pneumonia_copd'] = $this->query->sumAllocationsByQuarter('pneumonia_copd');
        $data['stroke'] = $this->query->sumAllocationsByQuarter('stroke');

        foreach ($data as $key => $row) {
            $tmp = [];

            for ($i = 0, $iMax = count($row); $i < $iMax; ++$i) {
                $tmp[$row[$i]['year'].'-'.$row[$i]['quarter']] = $row[$i]['value'];
            }

            $arr = array_reverse($tmp, true);
            $arr['caption'] = $key;

            $data[$key] = array_reverse($arr, true);
        }

        if (!ini_get('auto_detect_line_endings')) {
            ini_set('auto_detect_line_endings', '1');
        }

        $csv = Writer::createFromPath('php://temp', 'r+');
        $csv->setDelimiter(';');
        $csv->setOutputBOM(Writer::BOM_UTF8);

        $csv->insertOne(array_keys($data['total']));
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
            'dgina23-tracer-by-quarter-'.date('d-m-Y-H-i').'.csv'
        );

        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Description', 'File Transfer');
        $response->setCallback($contentCallback);
        $response->send();
    }
}
