<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile/{username}', name: 'app_user_profile')]
    public function index(User $user): Response
    {
        return $this->render('user/profile/show.html.twig', [
            'user' => $user,
        ]);
    }
}
