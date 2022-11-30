<?php

namespace App\Controller\Admin\Actions;

use App\Controller\Admin\UserCrudController;
use App\Entity\User;
use App\Service\Mailers\UserWelcomeMailerService;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/user/welcome/{id}', name: 'admin_user_welcome')]
class UserSendWelcomeMailController extends AbstractController
{
    public function __construct(
        private UserWelcomeMailerService $mailer,
        private AdminUrlGenerator $adminUrlGenerator
    ) {
    }

    public function __invoke(User $user): Response
    {
        try {
            $this->mailer->sendWelcomeEmail($user);
        } catch (HandlerFailedException $e) {
            $this->addFlash('danger', 'Could not send welcome E-Mail to '.$user->getUsername().': '.$e->getMessage());

            return $this->redirect($this->adminUrlGenerator->setController(UserCrudController::class)
                ->setAction('detail')
                ->setEntityId($user->getId())
                ->generateUrl());
        }

        $this->addFlash('success', 'Welcome E-Mail was successfully sent to '.$user->getUsername().'.');

        return $this->redirect($this->adminUrlGenerator->setController(UserCrudController::class)
            ->setAction('detail')
            ->setEntityId($user->getId())
            ->generateUrl());
    }
}
