<?php

namespace App\Controller;

use App\Factory\ExportFilterFactory;
use App\Form\ExportType;
use App\Query\ExportQuery;
use App\Service\FilterService;
use League\Csv\Writer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_USER')]
class ExportController extends AbstractController
{
    private FilterService $filterService;

    private ExportQuery $exportQuery;

    public function __construct(FilterService $filterService, ExportQuery $exportQuery)
    {
        $this->filterService = $filterService;
        $this->exportQuery = $exportQuery;
    }

    #[Route('/export', name: 'app_export_index')]
    public function index(Request $request, ExportFilterFactory $exportFilterFactory): Response
    {
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

        return $this->renderForm('export/index.html.twig', [
            'exportForm' => $exportForm,
            'results' => $results,
            'filters' => $this->filterService->getFilterDto(),
        ]);
    }

    #[Route('/export/fetch', name: 'app_export_fetch')]
    public function fetch(Request $request, ExportFilterFactory $exportFilterFactory): Response
    {
        $this->filterService->setRequest($request);
        $this->filterService->configureFilters($exportFilterFactory->getFilters());

        $results = $this->exportQuery->execute($this->filterService);

        $csv = Writer::createFromPath('php://temp', 'r+');
        $csv->insertOne([
            'id',
            'hospital',
            'createdAt',
            'arrivalAt',
            'urgency',
            'occasion',
            'assignment',
            'requiresResus',
            'requiresCathlab',
            'gender',
            'age',
            'isCPR',
            'isVentilated',
            'isShock',
            'isPregnant',
            'isInfectious',
            'isWorkAccident',
            'modeOfTransport',
            'speciality',
            'specialityDetail',
            'handoverPoint',
            'indication',
            'indicationCode',
            'secondaryIndication',
            'secondaryIndicationCode',
            'secondaryDeployment',
        ]);
        $csv->insertAll($results->getIterable());

        $flush_threshold = 1000; // the flush value should depend on your CSV size.
        $content_callback = function () use ($csv, $flush_threshold): void {
            foreach ($csv->chunk(1024) as $offset => $chunk) {
                echo $chunk;
                if (0 === $offset % $flush_threshold) {
                    flush();
                }
            }
        };

        $response = new StreamedResponse();
        $response->headers->set('Content-Encoding', 'none');
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');

        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'export.csv'
        );

        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Description', 'File Transfer');
        $response->setCallback($content_callback);

        return $response;
    }
}
