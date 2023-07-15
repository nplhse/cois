<?php

declare(strict_types=1);

namespace App\Controller\Data\Import;

use App\Entity\Import;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_USER')]
class ImportStatusController extends AbstractController
{
    #[Route(path: '/import/{id}/status', name: 'app_import_status')]
    public function status(Import $import): Response
    {
        $this->denyAccessUnlessGranted('create_import', $this->getUser());

        if ('pending' !== $import->getStatus()) {
            return $this->redirectToRoute('app_import_show', ['id' => $import->getId()]);
        }

        return $this->render('data/import/status.html.twig', [
            'import' => $import,
        ]);
    }
}
