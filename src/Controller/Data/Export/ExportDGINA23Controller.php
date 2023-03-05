<?php

declare(strict_types=1);

namespace App\Controller\Data\Export;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExportDGINA23Controller extends AbstractController
{
    #[Route('/export/dgina23', name: 'app_export_dgina23')]
    public function __invoke(): Response
    {
        return $this->render('data/export/dgina23.html.twig');
    }
}
