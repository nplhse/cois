<?php

namespace App\Controller\Settings;

use App\Domain\Command\Import\CreateImportCommand;
use App\Domain\Command\Import\DeleteImportCommand;
use App\Domain\Command\Import\ImportDataCommand;
use App\Domain\Command\Import\UpdateImportCommand;
use App\Domain\Event\Import\ImportFailedEvent;
use App\Entity\Import;
use App\Form\ImportType;
use App\Repository\AllocationRepository;
use App\Repository\ImportRepository;
use App\Service\Filters\HospitalFilter;
use App\Service\Filters\ImportFilter;
use App\Service\Filters\ImportOwnerFilter;
use App\Service\Filters\OrderFilter;
use App\Service\Filters\OwnHospitalFilter;
use App\Service\Filters\OwnImportFilter;
use App\Service\Filters\PageFilter;
use App\Service\Filters\SearchFilter;
use App\Service\FilterService;
use App\Service\PaginationFactory;
use App\Service\UploadService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_ADMIN')]
#[Route('/settings/import')]
class ImportController extends AbstractController
{
    private FilterService $filterService;

    private MessageBusInterface $messageBus;

    public function __construct(FilterService $filterService, MessageBusInterface $messageBus)
    {
        $this->filterService = $filterService;
        $this->messageBus = $messageBus;
    }

    #[Route('/', name: 'app_settings_import_index', methods: ['GET'])]
    public function index(Request $request, ImportRepository $importRepository): Response
    {
        $this->filterService->setRequest($request);
        $this->filterService->configureFilters([ImportFilter::Param, OwnImportFilter::Param, ImportOwnerFilter::Param, HospitalFilter::Param, OwnHospitalFilter::Param, PageFilter::Param, SearchFilter::Param, OrderFilter::Param]);

        $paginator = $importRepository->getImportPaginator($this->filterService);

        $args = [
            'action' => $this->generateUrl('app_settings_import_index'),
            'method' => 'GET',
        ];

        $importArguments = [
            'hidden' => [
                SearchFilter::Param => $this->filterService->getValue(SearchFilter::Param),
                OrderFilter::Param => $this->filterService->getValue(OrderFilter::Param),
            ],
        ];

        $importForm = $this->filterService->buildForm(ImportFilter::Param, array_merge($importArguments, $args));
        $importForm->handleRequest($request);

        $sortArguments = [
            'sortable' => ImportRepository::SORTABLE,
            'hidden' => [
                SearchFilter::Param => $this->filterService->getValue(SearchFilter::Param),
            ],
        ];

        $sortForm = $this->filterService->buildForm(OrderFilter::Param, array_merge($sortArguments, $args));
        $sortForm->handleRequest($request);

        $searchArguments = [
            'hidden' => [
                OrderFilter::Param => $this->filterService->getValue(OrderFilter::Param),
            ],
        ];

        $searchForm = $this->filterService->buildForm(SearchFilter::Param, array_merge($searchArguments, $args));
        $searchForm->handleRequest($request);

        return $this->renderForm('settings/import/index.html.twig', [
            'filters' => $this->filterService->getFilterDto(),
            'sortForm' => $sortForm,
            'searchForm' => $searchForm,
            'importForm' => $importForm,
            'imports' => $paginator,
            'pages' => PaginationFactory::create($this->filterService->getValue(PageFilter::Param), count($paginator), ImportRepository::PER_PAGE),
        ]);
    }

    #[Route('/new', name: 'app_settings_import_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UploadService $fileUploader, ImportRepository $importRepository, EventDispatcherInterface $eventDispatcher): Response
    {
        $import = new Import();
        $form = $this->createForm(ImportType::class, $import);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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

                return $this->render('settings/import/new.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

            // Refresh Import entity from database
            $import = $importRepository->findOneBy(['name' => $import->getName(), 'filePath' => $filePath]);

            $this->messageBus->dispatch(new ImportDataCommand($import->getId()));

            return $this->redirectToRoute('app_settings_import_show', ['id' => $import->getId()]);
        }

        return $this->renderForm('settings/import/new.html.twig', [
            'import' => $import,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_settings_import_show', methods: ['GET'])]
    public function show(Import $import): Response
    {
        return $this->render('settings/import/show.html.twig', [
            'import' => $import,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_settings_import_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Import $import, UploadService $fileUploader, EventDispatcherInterface $eventDispatcher): Response
    {
        $form = $this->createForm(ImportType::class, $import);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $import = $form->getData();

            /** @var ?UploadedFile $file */
            $file = $form->get('file')->getData();

            if (null !== $file) {
                $filePath = $fileUploader->uploadFile($file);

                $command = new UpdateImportCommand(
                    $import->getId(),
                    $import->getName(),
                    $import->getType(),
                    $import->getUser(),
                    $import->getHospital(),
                    true,
                    $filePath,
                    $file->getMimeType(),
                    $file->getClientOriginalExtension(),
                    $file->getSize()
                );

                $file = null;
            } else {
                $command = new UpdateImportCommand(
                    $import->getId(),
                    $import->getName(),
                    $import->getType(),
                    $import->getUser(),
                    $import->getHospital(),
                    false,
                    $import->getFilePath(),
                    $import->getMimeType(),
                    $import->getFileExtension(),
                    $import->getSize()
                );
            }

            try {
                $this->messageBus->dispatch($command);
            } catch (HandlerFailedException $e) {
                $eventDispatcher->dispatch(new ImportFailedEvent($import, $e), ImportFailedEvent::NAME);
                $this->addFlash('danger', 'Your import failed. We have send a notification to the admin to handle this issue.');

                return $this->render('import/new.html.twig', [
                    'form' => $form->createView(),
                ]);
            }

            if ($command->getUpdateFile()) {
                $this->messageBus->dispatch(new ImportDataCommand($import->getId()));
            }

            $this->addFlash('success', 'Your import was successfully edited.');

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
        $CsrfToken = $request->request->get('_token');

        if ($this->isCsrfTokenValid('delete'.$import->getId(), $CsrfToken)) {
            $command = new DeleteImportCommand($import->getId());

            try {
                $this->messageBus->dispatch($command);
            } catch (HandlerFailedException) {
                $this->addFlash('danger', 'Sorry! Could not delete Import: '.$import->getName());

                return $this->redirectToRoute('app_import_index');
            }
        }

        return $this->redirectToRoute('app_settings_import_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/refresh', name: 'app_settings_import_refresh')]
    public function refresh(Request $request, Import $import, AllocationRepository $allocationRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $this->messageBus->dispatch(new ImportDataCommand($import->getId()));

        $this->addFlash('success', 'Your import was successfully refreshed.');

        return $this->redirectToRoute('app_settings_import_show', ['id' => $import->getId()]);
    }
}
