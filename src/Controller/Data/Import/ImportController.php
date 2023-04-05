<?php

declare(strict_types=1);

namespace App\Controller\Data\Import;

use App\Entity\Import;
use App\Factory\ImportFilterFactory;
use App\Factory\OrderFilterFactory;
use App\Factory\PaginationFactory;
use App\Factory\SearchFilterFactory;
use App\Repository\ImportRepository;
use App\Service\Filters\HospitalFilter;
use App\Service\Filters\ImportStatusFilter;
use App\Service\Filters\OrderFilter;
use App\Service\Filters\OwnHospitalFilter;
use App\Service\Filters\OwnImportFilter;
use App\Service\Filters\PageFilter;
use App\Service\Filters\SearchFilter;
use App\Service\Filters\UserFilter;
use App\Service\FilterService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function App\Controller\Import\count;

#[IsGranted('ROLE_USER')]
class ImportController extends AbstractController
{
    public function __construct(
        private FilterService $filterService,
    ) {
    }

    #[Route(path: '/import/', name: 'app_import_index')]
    public function index(Request $request, ImportRepository $importRepository, ImportFilterFactory $importFilterFactory, OrderFilterFactory $orderFilterFactory, SearchFilterFactory $searchFilterFactory): Response
    {
        $this->filterService->setRequest($request);
        $this->filterService->configureFilters($importFilterFactory->getFilters());

        $paginator = $importRepository->getImportPaginator($this->filterService);

        $importFilterFactory->setHiddenFields([
            SearchFilter::Param => $this->filterService->getValue(SearchFilter::Param),
            OrderFilter::Param => $this->filterService->getValue(OrderFilter::Param),
        ]);

        $importForm = $importFilterFactory->setAction($this->generateUrl('app_import_index'))->getForm();
        $importForm->handleRequest($request);

        $orderFilterFactory->setHiddenFields([
            OwnImportFilter::Param => $this->filterService->getValue(OwnImportFilter::Param),
            ImportStatusFilter::Param => $this->filterService->getValue(ImportStatusFilter::Param),
            UserFilter::Param => $this->filterService->getValue(UserFilter::Param),
            HospitalFilter::Param => $this->filterService->getValue(HospitalFilter::Param),
            OwnHospitalFilter::Param => $this->filterService->getValue(OwnHospitalFilter::Param),
            SearchFilter::Param => $this->filterService->getValue(SearchFilter::Param),
        ]);

        $sortForm = $orderFilterFactory->setSortable(ImportRepository::SORTABLE)->setAction($this->generateUrl('app_import_index'))->getForm();

        $searchFilterFactory->setHiddenFields([
            OrderFilter::Param => $this->filterService->getValue(OrderFilter::Param),
        ]);

        $searchForm = $searchFilterFactory->setAction($this->generateUrl('app_import_index'))->getForm();
        $searchForm->handleRequest($request);

        return $this->renderForm('data/import/index.html.twig', [
            'filters' => $this->filterService->getFilterDto(),
            'sortForm' => $sortForm,
            'searchForm' => $searchForm,
            'importForm' => $importForm,
            'imports' => $paginator,
            'pages' => PaginationFactory::create($this->filterService->getValue(PageFilter::Param), \count($paginator), ImportRepository::PER_PAGE),
        ]);
    }
}
