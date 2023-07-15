<?php

declare(strict_types=1);

namespace App\Controller\Website;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GetStartedController extends AbstractController
{
    #[Route('/get_started', name: 'app_get_started')]
    public function index(): Response
    {
        return $this->render('website/get_started/overview.html.twig', [
            'controller_name' => 'GetStartedController',
        ]);
    }
}
