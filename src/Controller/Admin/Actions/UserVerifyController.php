<?php

namespace App\Controller\Admin\Actions;

use App\Controller\Admin\UserCrudController;
use App\Domain\Command\User\PromoteUserCommand;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/user/verify/{id}', name: 'admin_user_verify')]
class UserVerifyController extends AbstractController
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private AdminUrlGenerator $adminUrlGenerator
    ) {
    }

    public function __invoke(User $user): Response
    {
        $command = new PromoteUserCommand(
            $user->getId(),
            true,
            $user->isParticipant()
        );

        try {
            $this->messageBus->dispatch($command);
        } catch (HandlerFailedException $exception) {
            $this->addFlash('danger', 'Sorry, something went wrong.'.$exception->getMessage());
        }

        $this->addFlash('success', 'User has been promoted.');

        return $this->redirect($this->adminUrlGenerator->setController(UserCrudController::class)
            ->setAction('detail')
            ->setEntityId($user->getId())
            ->generateUrl());
    }
}
