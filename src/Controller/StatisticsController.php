<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_USER")
 */
class StatisticsController extends AbstractController
{
    #[Route('/statistics', name: 'app_statistics_index')]
    public function index(): Response
    {
        return $this->render('statistics/index.html.twig', [
        ]);
    }

    #[Route('/statistics/gender', name: 'app_statistics_gender')]
    public function demographics(): Response
    {
        return $this->render('statistics/gender.html.twig', [
        ]);
    }
}
