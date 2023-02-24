<?php

declare(strict_types=1);

namespace App\Controller\Data\Export;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExportController extends AbstractController
{
    #[Route('/export/', name: 'app_export_index')]
    public function index(): Response
    {
        return $this->render('data/export/index.html.twig', [
            'controller_name' => 'ExportController',
        ]);
    }
}
