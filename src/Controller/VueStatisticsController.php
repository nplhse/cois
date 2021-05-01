<?php

namespace App\Controller;

use App\Service\StatisticsService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

/**
 * @IsGranted("ROLE_USER")
 */
class VueStatisticsController extends AbstractController
{
    #[Route('/vuestats', name: 'vue_stats')]
    public function index(ChartBuilderInterface $chartBuilder): Response
    {
        return $this->render('statistics/vue.html.twig');
    }
}
