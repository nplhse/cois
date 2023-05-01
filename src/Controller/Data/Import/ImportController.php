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
use App\Service\ImportParameters;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_USER')]
class ImportController extends AbstractController
{
    public function __construct(
        private ImportRepository $importRepository,
    ) {
    }

    #[Route(path: '/import/', name: 'app_import_index')]
    public function index(Request $request): Response
    {
        $parameters = ImportParameters::createFromRequest($request);

        return $this->render('data/import/index.html.twig', [
            'imports' => $this->importRepository->getPaginator($parameters->getPage(), 'createdBy', 'desc'),
        ]);
    }
}
