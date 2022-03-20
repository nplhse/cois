<?php

namespace App\Controller;

use App\Query\ExportQuery\AllocationCovidQuery;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_USER')]
class ExportController extends AbstractController
{
    #[Route('/export', name: 'app_export_index')]
    public function index(AllocationCovidQuery $query): Response
    {
        return $this->render('export/index.html.twig');
    }
}
