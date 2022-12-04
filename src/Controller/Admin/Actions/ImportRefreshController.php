<?php

declare(strict_types=1);

namespace App\Controller\Admin\Actions;

use App\Controller\Admin\ImportCrudController;
use App\Domain\Command\Import\ImportDataCommand;
use App\Entity\Import;
use App\Repository\AllocationRepository;
use App\Repository\SkippedRowRepository;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/import/{id}/refresh', name: 'admin_import_refresh')]
class ImportRefreshController extends AbstractController
{
    public function __construct(
        private AllocationRepository $allocationRepository,
        private SkippedRowRepository $skippedRowRepository,
        private MessageBusInterface $messageBus,
        private AdminUrlGenerator $adminUrlGenerator
    ) {
    }

    public function __invoke(Import $import): Response
    {
        try {
            $this->allocationRepository->deleteByImport($import);
            $this->skippedRowRepository->deleteByImport($import);
            $this->messageBus->dispatch(new ImportDataCommand($import->getId()));
        } catch (HandlerFailedException $e) {
            $this->addFlash('danger', sprintf('Something went wrong! Failed to reload import %d: %s.', $import->getId(), $e->getMessage()));

            return $this->redirect($this->adminUrlGenerator->setController(ImportCrudController::class)
                ->setAction('detail')
                ->setEntityId($import->getId())
                ->generateUrl());
        }

        $this->addFlash('success', 'Your import was successfully refreshed.');

        return $this->redirect($this->adminUrlGenerator->setController(ImportCrudController::class)
            ->setAction('detail')
            ->setEntityId($import->getId())
            ->generateUrl());
    }
}
