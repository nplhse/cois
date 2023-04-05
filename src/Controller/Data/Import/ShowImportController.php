<?php

declare(strict_types=1);

namespace App\Controller\Data\Import;

use App\Entity\Import;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
