<?php

declare(strict_types=1);

namespace App\Controller\Data\Import;

use App\Factory\ImportFilterFactory;
use App\Factory\OrderFilterFactory;
use App\Factory\PaginationFactory;
use App\Factory\SearchFilterFactory;
use App\Repository\ImportRepository;
use App\Service\Filters\PageFilter;
use App\Service\FilterService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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

        return $this->render('data/import/index.html.twig', [
            'filters' => $this->filterService->getFilterDto(),
            'imports' => $paginator,
            'pages' => PaginationFactory::create($this->filterService->getValue(PageFilter::Param), \count($paginator), ImportRepository::PER_PAGE),
        ]);
    }
}
