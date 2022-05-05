<?php

namespace App\Controller\Exports;

use App\Query\ExportQuery\AllocationCovidQuery;
use League\Csv\Writer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_USER')]
class Covid19ExportController extends AbstractController
{
    #[Route('/export/covid19', name: 'app_export_c19')]
    public function showDetail(): Response
    {
        return $this->render('export/detail/covid19.html.twig');
    }

    #[Route('/export/covid19.csv', name: 'app_export_c19_file')]
    public function generateFile(AllocationCovidQuery $query): Response
    {
        $header = [];
        $indicationCodes = [124, 130, 310, 311, 312, 315, 326, 331, 332, 333, 348, 349, 354, 376, 377, 421, 422, 423, 511, 512, 513];
        $data = [];

        $header[0] = ['Name'];
        $header[1] = ['2019_01', '2019_02', '2019_03', '2019_04', '2019_05', '2019_06', '2019_07', '2019_08', '2019_09', '2019_10', '2019_11', '2019_12'];
        $header[2] = ['2020_01', '2020_02', '2020_03', '2020_04', '2020_05', '2020_06', '2020_07', '2020_08', '2020_09', '2020_10', '2020_11', '2020_12'];
        $header[3] = ['2021_01', '2021_02', '2021_03', '2021_04', '2021_05', '2021_06', '2021_07', '2021_08', '2021_09', '2021_10', '2021_11', '2021_12'];

        $data[] = array_merge($header[0], $header[1], $header[2], $header[3]);

        foreach ($indicationCodes as $code) {
            $result = $query->executeIndication($code, null);
            $temp = $this->buildResultArray($result->getItems());

            $data[] = $this->getValuesForRow($temp, 1, $code);
            $data[] = $this->getValuesForRow($temp, 2, $code);
            $data[] = $this->getValuesForRow($temp, 3, $code);
        }

        $result = $query->executeStats(null);
        $temp = $this->buildTotalsArray($result->getItems());

        $data[] = $this->getValuesForRow($temp, 0, 0);

        $csv = Writer::createFromPath('php://temp', 'r+');
        $csv->insertAll(new \ArrayIterator($data));

        // we create a callable to output the CSV in chunk
        // with Symfony StreamResponse you can flush the body content if necessary
        // see Symfony documentation for more information
        $flush_threshold = 1000; // the flush value should depend on your CSV size.
        $content_callback = function () use ($csv, $flush_threshold): void {
            foreach ($csv->chunk(1024) as $offset => $chunk) {
                echo $chunk;
                if (0 === $offset % $flush_threshold) {
                    flush();
                }
            }
        };

        // We send the CSV using Symfony StreamedResponse
        $response = new StreamedResponse();
        $response->headers->set('Content-Encoding', 'none');
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');

        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'ivena-export-covid19-'.date('Y-m-d-h-i').'.csv'
        );

        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Description', 'File Transfer');
        $response->setCallback($content_callback);

        return $response;
    }

    public function buildResultArray(array $results): array
    {
        $data = [];

        foreach ($results as $row) {
            $urgency[$row['urgency']] = $row['count'];
            $months[$row['creationMonth']] = $urgency;
            $data[$row['creationYear']] = $months;
        }

        return $this->fillGaps($data);
    }

    public function buildTotalsArray(array $results): array
    {
        $data = [];

        foreach ($results as $row) {
            $months[$row['creationMonth']] = $row['count'];
            $data[$row['creationYear']] = $months;
        }

        return $data;
    }

    public function fillGaps(array $data): array
    {
        for ($iy = 2019; $iy <= 2021; ++$iy) {
            for ($im = 1; $im <= 12; ++$im) {
                if (!isset($data[$iy][$im][1])) {
                    $data[$iy][$im][1] = 0;
                }
                if (!isset($data[$iy][$im][2])) {
                    $data[$iy][$im][2] = 0;
                }
                if (!isset($data[$iy][$im][3])) {
                    $data[$iy][$im][3] = 0;
                }
            }
        }

        return $data;
    }

    public function getValuesForRow(array $data, int $value = 0, int $code = 0): array
    {
        $result = [];
        $year = 2019;

        for ($i = 0; $i <= 36; ++$i) {
            if (0 !== $i) {
                if ($i <= 12) {
                    if ($value > 0) {
                        $result[] = $data[$year][$i][$value];
                    } else {
                        $result[] = $data[$year][$i];
                    }
                } elseif ($i <= 24) {
                    if ($value > 0) {
                        $result[] = $data[$year][$i - 12][$value];
                    } else {
                        $result[] = $data[$year][$i - 12];
                    }
                } else {
                    if ($value > 0) {
                        $result[] = $data[$year][$i - 24][$value];
                    } else {
                        $result[] = $data[$year][$i - 24];
                    }
                }
            } else {
                if ($value > 0) {
                    $result[] = $code.'_SK'.$value;
                } else {
                    $result[] = 'Total';
                }
            }
        }

        return $result;
    }
}
