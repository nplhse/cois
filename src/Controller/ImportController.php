<?php

declare(strict_types=1);

namespace App\Controller;

use App\Domain\Command\Import\CreateImportCommand;
use App\Domain\Command\Import\DeleteImportCommand;
use App\Domain\Command\Import\EditImportCommand;
use App\Domain\Command\Import\ImportDataCommand;
use App\Domain\Contracts\UserInterface;
use App\Domain\Event\Import\ImportFailedEvent;
use App\Entity\Import;
use App\Factory\ImportFilterFactory;
use App\Factory\OrderFilterFactory;
use App\Factory\PaginationFactory;
use App\Factory\SearchFilterFactory;
use App\Form\ImportType;
use App\Repository\ImportRepository;
use App\Repository\SkippedRowRepository;
use App\Service\Filters\HospitalFilter;
use App\Service\Filters\ImportStatusFilter;
use App\Service\Filters\OrderFilter;
use App\Service\Filters\OwnHospitalFilter;
use App\Service\Filters\OwnImportFilter;
use App\Service\Filters\PageFilter;
use App\Service\Filters\SearchFilter;
use App\Service\Filters\UserFilter;
use App\Service\FilterService;
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
#[Route(path: '/import')]
class ImportController extends AbstractController
{
    public function __construct(
        private FilterService $filterService,
        private MessageBusInterface $messageBus
    ) {
    }

    #[Route(path: '/', name: 'app_import_index')]
    public function index(Request $request, ImportRepository $importRepository, ImportFilterFactory $importFilterFactory, OrderFilterFactory $orderFilterFactory, SearchFilterFactory $searchFilterFactory): Response
    {
        $this->filterService->setRequest($request);
        $this->filterService->configureFilters($importFilterFactory->getFilters());

        $paginator = $importRepository->getImportPaginator($this->filterService);

        $importFilterFactory->setHiddenFields([
            SearchFilter::Param => $this->filterService->getValue(SearchFilter::Param),
            OrderFilter::Param => $this->filterService->getValue(OrderFilter::Param),
        ]);

        $importForm = $importFilterFactory->setAction($this->generateUrl('app_import_index'))->getForm();
        $importForm->handleRequest($request);

        $orderFilterFactory->setHiddenFields([
            OwnImportFilter::Param => $this->filterService->getValue(OwnImportFilter::Param),
            ImportStatusFilter::Param => $this->filterService->getValue(ImportStatusFilter::Param),
            UserFilter::Param => $this->filterService->getValue(UserFilter::Param),
            HospitalFilter::Param => $this->filterService->getValue(HospitalFilter::Param),
            OwnHospitalFilter::Param => $this->filterService->getValue(OwnHospitalFilter::Param),
            SearchFilter::Param => $this->filterService->getValue(SearchFilter::Param),
        ]);

        $sortForm = $orderFilterFactory->setSortable(ImportRepository::SORTABLE)->setAction($this->generateUrl('app_import_index'))->getForm();

        $searchFilterFactory->setHiddenFields([
            OrderFilter::Param => $this->filterService->getValue(OrderFilter::Param),
        ]);

        $searchForm = $searchFilterFactory->setAction($this->generateUrl('app_import_index'))->getForm();
        $searchForm->handleRequest($request);

        return $this->renderForm('import/index.html.twig', [
            'filters' => $this->filterService->getFilterDto(),
            'sortForm' => $sortForm,
            'searchForm' => $searchForm,
            'importForm' => $importForm,
            'imports' => $paginator,
            'pages' => PaginationFactory::create($this->filterService->getValue(PageFilter::Param), count($paginator), ImportRepository::PER_PAGE),
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

                return $this->render('import/new.html.twig', [
                    'form' => $form,
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

    #[Route(path: '/{id}', name: 'app_import_show', methods: ['GET'])]
    public function show(Import $import): Response
    {
        return $this->render('import/show.html.twig', [
            'import' => $import,
        ]);
    }

    #[Route(path: '/{id}/skipped', name: 'app_import_show_skipped', methods: ['GET'])]
    public function showSkipped(Import $import, SkippedRowRepository $skippedRowRepository): Response
    {
        $this->denyAccessUnlessGranted('delete', $import);

        $results = $skippedRowRepository->findBy(['import' => $import]);

        return $this->render('import/show_skipped.html.twig', [
            'import' => $import,
            'results' => $results,
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
