<?php

declare(strict_types=1);

namespace App\Controller\Data\Export;

use App\Factory\ExportFilterFactory;
use App\Form\ExportType;
use App\Query\AllocationExportQuery;
use App\Service\FilterService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
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
    public function fetch(Request $request, ExportFilterFactory $exportFilterFactory): Response
    {
        $this->denyAccessUnlessGranted('export', $this->getUser());

        $this->filterService->setRequest($request);
        $this->filterService->configureFilters($exportFilterFactory->getFilters());

        $results = $this->exportQuery->execute($this->filterService);

        $header = [
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

        $true = $this->translator->trans('True', [], 'domain');
        $false = $this->translator->trans('False', [], 'domain');

        $fp = fopen('php://temp', 'w');
        fputcsv($fp, $header);
        foreach ($results as $fields) {
            $fields['urgency'] = 'SK'.$fields['urgency'];
            $fields['requiresResus'] = (true == $fields['requiresResus']) ? $true : $false;
            $fields['requiresCathlab'] = (true == $fields['requiresCathlab']) ? $true : $false;
            $fields['isCPR'] = (true == $fields['isCPR']) ? $true : $false;
            $fields['isVentilated'] = (true == $fields['isVentilated']) ? $true : $false;
            $fields['isShock'] = (true == $fields['isShock']) ? $true : $false;
            $fields['isPregnant'] = (true == $fields['isPregnant']) ? $true : $false;
            $fields['isWorkAccident'] = (true == $fields['isWorkAccident']) ? $true : $false;
            $fields['isWithPhysician'] = (true == $fields['isWithPhysician']) ? $true : $false;
            $fields['specialityWasClosed'] = (true == $fields['specialityWasClosed']) ? $true : $false;
            $fields['secondaryIndicationCode'] = (0 === $fields['secondaryIndicationCode']) ? '' : $fields['secondaryIndicationCode'];

            fputcsv($fp, $fields);
        }

        rewind($fp);
        $response = new Response(stream_get_contents($fp));
        fclose($fp);

        $response->headers->set('Content-Encoding', 'none');
        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');

        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'export.csv'
        );

        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Description', 'File Transfer');

        return $response;
    }
}
