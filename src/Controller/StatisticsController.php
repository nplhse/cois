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
    public function gender(): Response
    {
        return $this->render('statistics/gender.html.twig', [
        ]);
    }

    #[Route('/statistics/times', name: 'app_statistics_times')]
    public function times(): Response
    {
        return $this->render('statistics/times.html.twig', [
        ]);
    }

    #[Route('/statistics/pzc', name: 'app_statistics_pzc')]
    public function pzc(): Response
    {
        return $this->render('statistics/pzc.html.twig', [
        ]);
    }

    #[Route('/statistics/urgency', name: 'app_statistics_urgency')]
    public function urgency(): Response
    {
        return $this->render('statistics/urgency.html.twig', [
        ]);
    }
}
