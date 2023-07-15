<?php

declare(strict_types=1);

namespace App\Controller\Website;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FeatureController extends AbstractController
{
    public function __construct(
        private bool $appEnableFeatures,
    ) {
    }

    #[Route(path: '/features', name: 'app_page_features')]
    public function index(): Response
    {
        if (false === $this->appEnableFeatures) {
            throw $this->createNotFoundException('This page does not exist.');
        }

        return $this->render('website/features/features.html.twig');
    }
}
