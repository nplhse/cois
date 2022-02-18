<?php

namespace App\Controller\Settings;

use App\Domain\Command\Task\ImportReminderCommand;
use App\Domain\Event\Task\TaskFailedEvent;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    #[Route('/settings/task/', name: 'app_settings_task_index')]
    public function index(): Response
    {
        return $this->render('settings/task/index.html.twig');
    }

    #[Route('/settings/task/run/importReminder', name: 'app_settings_task_import_reminder')]
    public function importReminder(UserRepository $userRepository, EventDispatcherInterface $eventDispatcher): Response
    {
        $recipients = $userRepository->findHospitalOwners();

        $command = new ImportReminderCommand($recipients);

        try {
            $this->messageBus->dispatch($command);
        } catch (HandlerFailedException $e) {
            $eventDispatcher->dispatch(new TaskFailedEvent($e), TaskFailedEvent::NAME);
            $this->addFlash('danger', 'Something went wrong! We have send a notification to the admin to handle this issue.');

            return $this->redirectToRoute('app_settings_task_index');
        }

        $this->addFlash('success', 'Import Reminder successfully sent to Hospital owners.');

        return $this->redirectToRoute('app_settings_task_index');
    }
}
