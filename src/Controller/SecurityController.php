<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SecurityLoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: 'login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        if (!$this->getUser()) {
            $lastUsername = $authenticationUtils->getLastUsername();
        } else {
            /** @var User $user */
            $user = $this->getUser();
            $lastUsername = $user->getUsername();
        }

        $form = $this->createForm(
            SecurityLoginType::class,
            [
                'username' => $lastUsername,
            ]
        );

        return $this->render(
            'security/login.html.twig',
            [
                'form' => $form->createView(),
                'error' => $error,
            ]
        );
    }

    #[Route(path: 'logout', name: 'app_logout')]
    public function logout(): Response
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
