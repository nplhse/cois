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
class ShowImportController extends AbstractController
{
    #[Route(path: '/import/{id}', name: 'app_import_show', methods: ['GET'])]
    public function show(Import $import): Response
    {
        return $this->render('data/import/show.html.twig', [
            'import' => $import,
        ]);
    }
}
