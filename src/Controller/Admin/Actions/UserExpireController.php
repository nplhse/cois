<?php

namespace App\Controller\Admin\Actions;

use App\Controller\Admin\UserCrudController;
use App\Domain\Command\User\ExpireUserCommand;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/user/expire/{id}', name: 'admin_user_expire')]
class UserExpireController extends AbstractController
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private AdminUrlGenerator $adminUrlGenerator
    ) {
    }

    public function __invoke(User $user): Response
    {
        $command = new ExpireUserCommand(
            $user->getId(),
        );

        try {
            $this->messageBus->dispatch($command);
        } catch (HandlerFailedException $exception) {
            $this->addFlash('danger', 'Sorry, something went wrong.'.$exception->getMessage());
        }

        $this->addFlash('success', 'User credentials have been expired.');

        return $this->redirect($this->adminUrlGenerator->setController(UserCrudController::class)
            ->setAction('detail')
            ->setEntityId($user->getId())
            ->generateUrl());
    }
}
