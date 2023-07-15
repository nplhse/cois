<?php

declare(strict_types=1);

namespace App\Controller\Data\Export;

use App\Factory\ExportFilterFactory;
use App\Form\ExportType;
use App\Query\AllocationExportQuery;
use App\Service\FilterService;
use League\Csv\Reader;
use League\Csv\Writer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[IsGranted('ROLE_USER')]
class ExportAllocationController extends AbstractController
{
    public function __construct(
        private FilterService $filterService,
        private AllocationExportQuery $exportQuery,
        private TranslatorInterface $translator
    ) {
    }

    #[Route('/export/allocation', name: 'app_export_allocation')]
    public function index(Request $request, ExportFilterFactory $exportFilterFactory): Response
    {
        $this->denyAccessUnlessGranted('export', $this->getUser());

        $this->filterService->setRequest($request);
        $this->filterService->configureFilters($exportFilterFactory->getFilters());

        $results = null;

        $exportForm = $this->createForm(ExportType::class, null, [
            'method' => 'GET',
        ]);
        $exportForm->handleRequest($request);

        if ($exportForm->isSubmitted() && $exportForm->isValid()) {
            $results = $this->exportQuery->count($this->filterService);
        }

        return $this->render('data/export/allocation.html.twig', [
            'exportForm' => $exportForm,
            'results' => $results,
            'filters' => $this->filterService->getFilterDto(),
        ]);
    }

    #[Route('/export/allocation/fetch', name: 'app_export_allocation_fetch')]
    public function fetch(Request $request, ExportFilterFactory $exportFilterFactory): void
    {
        $this->denyAccessUnlessGranted('export', $this->getUser());

        $this->filterService->setRequest($request);
        $this->filterService->configureFilters($exportFilterFactory->getFilters());

        if (!ini_get('auto_detect_line_endings')) {
            ini_set('auto_detect_line_endings', '1');
        }

        $csv = Writer::createFromPath('php://temp', 'r+');
        $csv->setDelimiter(';');
        $csv->setOutputBOM(Reader::BOM_UTF8);

        $fieldFormatter = function (array $row): array {
            $true = $this->translator->trans('True', [], 'domain');
            $false = $this->translator->trans('False', [], 'domain');

            $row['urgency'] = 'SK'.$row['urgency'];
            $row['requiresResus'] = (true == $row['requiresResus']) ? $true : $false;
            $row['requiresCathlab'] = (true == $row['requiresCathlab']) ? $true : $false;
            $row['isCPR'] = (true == $row['isCPR']) ? $true : $false;
            $row['isVentilated'] = (true == $row['isVentilated']) ? $true : $false;
            $row['isShock'] = (true == $row['isShock']) ? $true : $false;
            $row['isPregnant'] = (true == $row['isPregnant']) ? $true : $false;
            $row['isWorkAccident'] = (true == $row['isWorkAccident']) ? $true : $false;
            $row['isWithPhysician'] = (true == $row['isWithPhysician']) ? $true : $false;
            $row['specialityWasClosed'] = (true == $row['specialityWasClosed']) ? $true : $false;
            $row['secondaryIndicationCode'] = (0 === $row['secondaryIndicationCode']) ? '' : $row['secondaryIndicationCode'];

            return $row;
        };

        $csv->insertOne($this->getHeaders());
        $csv->addFormatter($fieldFormatter);
        $csv->insertAll($this->exportQuery->execute($this->filterService));

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
            'ivena-allocation-export.csv'
        );

        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Description', 'File Transfer');
        $response->setCallback($contentCallback);
        $response->send();
    }

    public function getHeaders(): array
    {
        return [
            $this->translator->trans('Hospital', [], 'domain'),
            $this->translator->trans('Created at', [], 'domain'),
            $this->translator->trans('Arrival at', [], 'domain'),
            $this->translator->trans('Gender', [], 'domain'),
            $this->translator->trans('Age', [], 'domain'),
            $this->translator->trans('Urgency', [], 'domain'),
            $this->translator->trans('Occasion', [], 'domain'),
            $this->translator->trans('Assignment', [], 'domain'),
            $this->translator->trans('Requires Resus', [], 'domain'),
            $this->translator->trans('Requires Cathlab', [], 'domain'),
            $this->translator->trans('Is CPR', [], 'domain'),
            $this->translator->trans('Is ventilated', [], 'domain'),
            $this->translator->trans('Is shock', [], 'domain'),
            $this->translator->trans('Is infectious', [], 'domain'),
            $this->translator->trans('Is pregnant', [], 'domain'),
            $this->translator->trans('Is work accident', [], 'domain'),
            $this->translator->trans('Is with physician', [], 'domain'),
            $this->translator->trans('Mode of transport', [], 'domain'),
            $this->translator->trans('Speciality', [], 'domain'),
            $this->translator->trans('Speciality Detail', [], 'domain'),
            $this->translator->trans('Speciality was closed', [], 'domain'),
            $this->translator->trans('Indication code', [], 'domain'),
            $this->translator->trans('Indication', [], 'domain'),
            $this->translator->trans('Secondary indication code', [], 'domain'),
            $this->translator->trans('Secondary indication', [], 'domain'),
            $this->translator->trans('Secondary deployment', [], 'domain'),
        ];
    }
}
