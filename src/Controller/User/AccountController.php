<?php

namespace App\Controller\User;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user")
 * @IsGranted("ROLE_USER")
 */
class AccountController extends AbstractController
{
    /**
     * @Route("/", name="account")
     */
    public function index(Request $request): Response
    {
        $user = $this->getUser();

        return $this->render('user/account.html.twig', [
            'user' => $user,
        ]);
    }
}
