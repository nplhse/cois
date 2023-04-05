<?php

declare(strict_types=1);

namespace App\Controller\Data\Import;

use App\Domain\Command\Import\CreateImportCommand;
use App\Domain\Command\Import\ImportDataCommand;
use App\Domain\Contracts\UserInterface;
use App\Domain\Event\Import\ImportFailedEvent;
use App\Entity\Import;
use App\Form\ImportType;
use App\Repository\ImportRepository;
use App\Service\Import\UploadService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_USER')]
class CreateImportController extends AbstractController
{
    public function __construct(
        private MessageBusInterface $messageBus
    ) {
    }

    #[Route(path: '/import/new', name: 'app_import_new')]
    public function new(Request $request, UploadService $fileUploader, ImportRepository $importRepository, EventDispatcherInterface $eventDispatcher): Response
    {
        $this->denyAccessUnlessGranted('create_import', $this->getUser());

        $import = new Import();

        $form = $this->createForm(ImportType::class, $import);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UserInterface $user */
            $user = $this->getUser();
            $import->setUser($user);

            /** @var UploadedFile $file */
            $file = $form->get('file')->getData();
            $filePath = $fileUploader->uploadFile($file);

            $command = new CreateImportCommand(
                $import->getName(),
                $import->getType(),
                $import->getUser(),
                $import->getHospital(),
                $filePath,
                $file->getMimeType(),
                $file->getClientOriginalExtension(),
                $file->getSize()
            );

            try {
                $this->messageBus->dispatch($command);
            } catch (HandlerFailedException $e) {
                $eventDispatcher->dispatch(new ImportFailedEvent($import, $e), ImportFailedEvent::NAME);
                $this->addFlash('danger', 'Your import failed. We have send a notification to the admin to handle this issue.');

                return $this->render('data/import/new.html.twig', [
                    'form' => $form,
                ]);
            }

            // Refresh Import entity from database
            $import = $importRepository->findOneBy(['name' => $import->getName(), 'filePath' => $filePath]);

            $this->messageBus->dispatch(new ImportDataCommand($import->getId()));

            return $this->redirectToRoute('app_import_show', ['id' => $import->getId()]);
        }

        return $this->render('data/import/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
