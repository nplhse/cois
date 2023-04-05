<?php

declare(strict_types=1);

namespace App\Controller\Data\Import;

use App\Domain\Command\Import\DeleteImportCommand;
use App\Entity\Import;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_USER')]
class DeleteImportController extends AbstractController
{
    public function __construct(
        private MessageBusInterface $messageBus
    ) {
    }

    #[Route(path: '/import/{id}/delete', name: 'app_import_delete_form', methods: ['GET'])]
    public function deleteForm(Import $import): Response
    {
        $this->denyAccessUnlessGranted('delete', $import);

        return $this->render('data/import/_delete_form.html.twig', [
            'import' => $import,
        ]);
    }

    #[Route(path: '/import/{id}/delete', name: 'app_import_delete', methods: ['POST'])]
    public function delete(Import $import, Request $request): Response
    {
        $this->denyAccessUnlessGranted('delete', $import);

        $CsrfToken = (string) $request->request->get('_token');

        if ($this->isCsrfTokenValid('delete'.$import->getId(), $CsrfToken)) {
            $command = new DeleteImportCommand($import->getId());

            try {
                $this->messageBus->dispatch($command);
            } catch (HandlerFailedException) {
                $this->addFlash('danger', 'Sorry! Could not delete Import: '.$import->getName());

                return $this->redirectToRoute('app_import_index');
            }
        }

        $this->addFlash('danger', 'Your import was successfully deleted.');

        return $this->redirectToRoute('app_import_index');
    }
}
