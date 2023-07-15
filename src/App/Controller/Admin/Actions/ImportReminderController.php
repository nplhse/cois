<?php

declare(strict_types=1);

namespace App\Controller\Admin\Actions;

use App\Controller\Admin\UserCrudController;
use App\Repository\UserRepository;
use Domain\Command\Task\ImportReminderCommand;
use Domain\Event\Task\TaskFailedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/import/reminder', name: 'admin_import_reminder')]
class ImportReminderController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private MessageBusInterface $messageBus,
        private EventDispatcherInterface $eventDispatcher,
        private AdminUrlGenerator $adminUrlGenerator
    ) {
    }

    public function __invoke(): Response
    {
        $recipients = $this->userRepository->findHospitalOwners();

        $command = new ImportReminderCommand($recipients);

        try {
            $this->messageBus->dispatch($command);
        } catch (HandlerFailedException $e) {
            $this->eventDispatcher->dispatch(new TaskFailedEvent($e), TaskFailedEvent::NAME);
            $this->addFlash('danger', 'Something went wrong! We have send a notification to the admin to handle this issue.');

            return $this->redirect($this->adminUrlGenerator->setController(UserCrudController::class)
                ->setAction('index')
                ->generateUrl());
        }

        $this->addFlash('success', 'Import Reminder successfully sent to Hospital owners.');

        return $this->redirect($this->adminUrlGenerator->setController(UserCrudController::class)
            ->setAction('index')
            ->generateUrl());
    }
}
