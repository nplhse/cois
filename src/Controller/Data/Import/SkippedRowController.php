<?php

declare(strict_types=1);

namespace App\Controller\Data\Import;

use App\Entity\Import;
use App\Repository\SkippedRowRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_USER')]
class SkippedRowController extends AbstractController
{
    #[Route(path: 'import/{id}/skipped', name: 'app_import_show_skipped', methods: ['GET'])]
    public function show(Import $import, SkippedRowRepository $skippedRowRepository): Response
    {
        $this->denyAccessUnlessGranted('delete', $import);

        $results = $skippedRowRepository->findBy(['import' => $import]);

        return $this->render('data/import/skipped_rows/show.html.twig', [
            'import' => $import,
            'results' => $results,
        ]);
    }
}
