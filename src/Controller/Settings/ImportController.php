<?php

namespace App\Controller\Settings;

use App\Entity\Import;
use App\Form\ImportType;
use App\Message\ImportDataMessage;
use App\Repository\AllocationRepository;
use App\Repository\ImportRepository;
use App\Service\FileUploader;
use App\Service\RequestParamService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/settings/import')]
class ImportController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    private MessageBusInterface $messageBus;

    public function __construct(EntityManagerInterface $entityManager, MessageBusInterface $messageBus)
    {
        $this->entityManager = $entityManager;
        $this->messageBus = $messageBus;
    }

    #[Route('/', name: 'app_settings_import_index', methods: ['GET'])]
    public function index(Request $request, ImportRepository $importRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $paramService = new RequestParamService($request);

        $filters = [];
        $filters['search'] = $paramService->getSearch();
        $filters['page'] = $paramService->getPage();

        $filters['user'] = $paramService->getUser();
        $filters['hospital'] = $paramService->getHospital();
        $filters['show'] = 'all';

        $paginator = $importRepository->getImportPaginator($paramService->getPage(), $filters);

        return $this->render('settings/import/index.html.twig', [
            'imports' => $paginator,
            'search' => $filters['search'],
            'filters' => $filters,
            'pages' => $paramService->getPagination(count($paginator), $paramService->getPage(), ImportRepository::PAGINATOR_PER_PAGE),
        ]);
    }

    #[Route('/new', name: 'app_settings_import_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FileUploader $fileUploader): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $import = new Import();
        $form = $this->createForm(ImportType::class, $import);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $import = $form->getData();

            /** @var UploadedFile $file */
            $file = $form->get('file')->getData();
            $fileData = $fileUploader->uploadFile($file);

            $import->setName($fileData['uniqueName']);
            $import->setExtension($file->getClientOriginalExtension());
            $import->setPath($fileData['path']);
            $import->setMimeType($file->getMimeType());
            $import->setSize($file->getSize());
            $import->setCreatedAt(new \DateTime('NOW'));
            $import->setIsFixture(false);
            $import->setFile(null);
            $import->setStatus('pending');

            $this->entityManager->persist($import);
            $this->entityManager->flush();

            try {
                $this->messageBus->dispatch(new ImportDataMessage($import, $import->getHospital()));

                $this->addFlash('success', 'Your import was successfully created.');
            } catch (HandlerFailedException $e) {
                $import->setStatus('failed');
                $import->setLastError($e->getMessage());
                $import->setLastRun(new \DateTime('NOW'));

                $this->entityManager->persist($import);
                $this->entityManager->flush();

                $this->addFlash('danger', 'Your import failed, see details for more information. We have send a notification to the admin to handle this issue.');
            }

            return $this->redirectToRoute('app_settings_import_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('settings/import/new.html.twig', [
            'import' => $import,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_settings_import_show', methods: ['GET'])]
    public function show(Import $import): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('settings/import/show.html.twig', [
            'import' => $import,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_settings_import_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Import $import, FileUploader $fileUploader): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(ImportType::class, $import);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $import = $form->getData();

            /** @var UploadedFile|null $file */
            $file = $form->get('file')->getData();

            if ($file) {
                $fileData = $fileUploader->uploadFile($file);

                $import->setName($fileData['uniqueName']);
                $import->setExtension($file->getClientOriginalExtension());
                $import->setPath($fileData['path']);
                $import->setMimeType($file->getMimeType());
                $import->setSize($file->getSize());
                $import->setCreatedAt(new \DateTime('NOW'));
                $import->setFile(null);
                $import->setStatus('pending');

                try {
                    $this->messageBus->dispatch(new ImportDataMessage($import, $import->getHospital()));

                    $this->addFlash('success', 'Your import was successfully edited.');
                } catch (HandlerFailedException $e) {
                    $import->setStatus('failed');
                    $import->setLastError($e->getMessage());
                    $import->setLastRun(new \DateTime('NOW'));

                    $this->entityManager->persist($import);
                    $this->entityManager->flush();

                    $this->addFlash('danger', 'Your import failed, see details for more information. We have send a notification to the admin to handle this issue.');
                }
            }

            $this->entityManager->persist($import);
            $this->entityManager->flush();

            if (null === $file) {
                $this->addFlash('success', 'Your import was successfully edited.');
            }

            return $this->redirectToRoute('app_settings_import_show', ['id' => $import->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('settings/import/edit.html.twig', [
            'import' => $import,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_settings_import_delete', methods: ['POST'])]
    public function delete(Request $request, Import $import): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $CsrfToken = $request->request->get('_token');

        if ($this->isCsrfTokenValid('delete'.$import->getId(), $CsrfToken)) {
            $this->entityManager->remove($import);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_settings_import_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/refresh', name: 'app_settings_import_refresh')]
    public function refresh(Request $request, Import $import, AllocationRepository $allocationRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $allocationRepository->deleteByImport($import);

        $user = $import->getUser();

        if (!$import->getHospital()) {
            $hospital = $user->getHospital();
        } else {
            $hospital = $import->getHospital();
        }

        $command = new ImportDataMessage($import, $hospital);

        try {
            $this->messageBus->dispatch(new ImportDataMessage($import, $import->getHospital()));

            $this->addFlash('success', 'Refreshed Import in database.');
        } catch (HandlerFailedException $e) {
            $import->setStatus('failed');
            $import->setLastError($e->getMessage());
            $import->setLastRun(new \DateTime('NOW'));

            $this->entityManager->persist($import);
            $this->entityManager->flush();

            $this->addFlash('danger', 'Import could not be refreshed, see details for more information.');
        }

        return $this->redirectToRoute('app_settings_import_show', ['id' => $import->getId()]);
    }
}
