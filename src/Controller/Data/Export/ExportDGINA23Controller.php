<?php

declare(strict_types=1);

namespace App\Controller\Data\Export;

use App\Query\Export\DGINA23ExportQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExportDGINA23Controller extends AbstractController
{
    public function __construct(
        private DGINA23ExportQuery $query
    ) {
    }

    #[Route('/export/dgina23', name: 'app_export_dgina23')]
    public function __invoke(): Response
    {
        $data = [];

        $data['total'] = $this->query->new()->findAllocationsBySize()->getResults();

        return $this->render('data/export/dgina23.html.twig', [
            'data' => $data,
        ]);
    }
}
