<?php

declare(strict_types=1);

namespace App\Controller\Import;

use App\Domain\Command\Import\EditImportCommand;
use App\Entity\Import;
use App\Form\ImportType;
use App\Repository\ImportRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_USER')]
class EditImportController extends AbstractController
{
    public function __construct(
        private MessageBusInterface $messageBus
    ) {
    }

    #[Route(path: '/import/edit/{id}', name: 'app_import_edit')]
    public function edit(Import $import, Request $request, ImportRepository $importRepository): Response
    {
        $this->denyAccessUnlessGranted('edit', $import);

        $form = $this->createForm(ImportType::class, $import, ['create' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $command = new EditImportCommand(
                $import->getId(),
                $import->getName(),
            );

            try {
                $this->messageBus->dispatch($command);
            } catch (HandlerFailedException) {
                $this->addFlash('danger', 'Editing your import failed. Please try again later.');

                return $this->render('import/edit.html.twig', [
                    'import' => $import,
                    'form' => $form,
                ]);
            }

            $this->addFlash('success', 'Your import was successfully updated.');

            return $this->redirectToRoute('app_import_show', ['id' => $import->getId()]);
        }

        return $this->render('import/edit.html.twig', [
            'import' => $import,
            'form' => $form->createView(),
        ]);
    }
}
