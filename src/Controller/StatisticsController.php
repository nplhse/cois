<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;

/**
 * @IsGranted("ROLE_USER")
 */
class StatisticsController extends AbstractController
{
    #[Route('/statistics', name: 'app_statistics_index')]
    public function index(ChartBuilderInterface $chartBuilder): Response
    {
        return $this->render('statistics/index.html.twig', [
        ]);
    }

    #[Route('/statistics/age', name: 'app_statistics_age')]
    public function demographics(ChartBuilderInterface $chartBuilder): Response
    {
        return $this->render('statistics/age.html.twig', [
        ]);
    }
}
