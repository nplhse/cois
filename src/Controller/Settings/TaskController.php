<?php

namespace App\Controller\Settings;

use App\Message\SendImportReminderMessage;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    #[Route('/settings/task/', name: 'app_settings_task_index')]
    public function index(): Response
    {
        return $this->render('settings/task/index.html.twig');
    }

    #[Route('/settings/task/run/1', name: 'app_settings_task_run1')]
    public function run1(UserRepository $userRepository): Response
    {
        $recipients = $userRepository->getHospitalOwnerRecipients();

        $message = new SendImportReminderMessage($recipients);

        dump($message);

        $this->dispatchMessage($message);

        $this->addFlash('success', 'Import Reminder successfully sent to Hospital owners.');

        return $this->redirectToRoute('app_settings_task_index');
    }
}
