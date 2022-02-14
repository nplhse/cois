<?php

namespace App\Controller;

use App\Domain\Command\Import\CreateImportCommand;
use App\Domain\Command\Import\EditImportCommand;
use App\Domain\Command\Import\ImportDataCommand;
use App\Domain\Contracts\UserInterface;
use App\Domain\Event\Import\ImportFailedEvent;
use App\Entity\Import;
use App\Form\ImportType;
use App\Repository\AllocationRepository;
use App\Repository\HospitalRepository;
use App\Repository\ImportRepository;
use App\Service\RequestParamService;
use App\Service\UploadService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_USER")
 */
#[Route(path: '/import')]
class ImportController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    private MessageBusInterface $messageBus;

    public function __construct(EntityManagerInterface $entityManager, MessageBusInterface $messageBus)
    {
        $this->entityManager = $entityManager;
        $this->messageBus = $messageBus;
    }

    #[Route(path: '/', name: 'app_import_index')]
    public function index(Request $request, UploadService $fileUploader, ImportRepository $importRepository, HospitalRepository $hospitalRepository): Response
    {
        $hospital = $hospitalRepository->findOneBy(['owner' => $this->getUser()->getId()]);

        $paramService = new RequestParamService($request);

        $filters = [];
        $filters['search'] = $paramService->getSearch();
        $filters['page'] = $paramService->getPage();
        $filters['show'] = $paramService->getShow();
        $filters['user'] = $this->getUser();
        $filters['hospital'] = $this->getUser()->getHospital();
        $filters['sortBy'] = $paramService->getSortBy();
        $filters['orderBy'] = $paramService->getOrderBy();

        $paginator = $importRepository->getImportPaginator($paramService->getPage(), $filters);

        return $this->render('import/index.html.twig', [
            'imports' => $paginator,
            'pages' => $paramService->getPagination(count($paginator), $paramService->getPage(), ImportRepository::PAGINATOR_PER_PAGE),
            'filters' => $filters,
            'filterIsSet' => $paramService->isFilterIsSet(),
        ]);
    }

    #[Route(path: '/new', name: 'app_import_new')]
    public function new(Request $request, UploadService $fileUploader, ImportRepository $importRepository, EventDispatcherInterface $eventDispatcher): Response
    {
        $this->denyAccessUnlessGranted('create_import', $this->getUser());

        $import = new Import();

        $form = $this->createForm(ImportType::class, $import);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->isGranted('ROLE_ADMIN')) {
                /** @var UserInterface $user */
                $user = $this->getUser();
                $import->setUser($user);
            }

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

                return $this->render('import/new.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

            // Refresh Import entity from database
            $import = $importRepository->findOneBy(['name' => $import->getName(), 'filePath' => $filePath]);

            $this->messageBus->dispatch(new ImportDataCommand($import->getId()));

            return $this->redirectToRoute('app_import_show', ['id' => $import->getId()]);
        }

        return $this->render('import/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/edit/{id}', name: 'app_import_edit')]
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
                    'form' => $form->createView(),
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

    #[Route(path: '/{id}', name: 'app_import_show', methods: ['GET'])]
    public function show(Import $import): Response
    {
        $userIsOwner = $import->getUser() == $this->getUser();

        return $this->render('import/show.html.twig', [
            'import' => $import,
            'user_is_owner' => $userIsOwner,
        ]);
    }

    #[Route(path: '/{id}/delete', name: 'app_import_delete_form', methods: ['GET'])]
    public function deleteForm(Import $import): Response
    {
        $this->denyAccessUnlessGranted('delete', $import);

        return $this->render('import/_delete_form.html.twig', [
            'import' => $import,
        ]);
    }

    #[Route(path: '/{id}/delete', name: 'app_import_delete', methods: ['POST'])]
    public function delete(Import $import, AllocationRepository $allocationRepository, ImportRepository $importRepository): Response
    {
        $this->denyAccessUnlessGranted('delete', $import);

        $allocationRepository->deleteByImport($import);
        $importRepository->delete($import);

        $this->addFlash('danger', 'Your import was successfully deleted.');

        return $this->redirectToRoute('app_import_index');
    }
}
