<?php

namespace App\Controller;

use App\DataTransferObjects\PZCStatisticsDto;
use App\Query\AllocationQuery;
use App\Repository\HospitalRepository;
use App\Service\RequestParamService;
use App\Service\StatisticsService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_USER")
 */
class StatisticsController extends AbstractController
{
    private HospitalRepository $hospitalRepository;

    private AllocationQuery $allocationQuery;

    private StatisticsService $statisticsService;

    public function __construct(HospitalRepository $hospitalRepository, AllocationQuery $allocationQuery, StatisticsService $statisticsService)
    {
        $this->hospitalRepository = $hospitalRepository;
        $this->allocationQuery = $allocationQuery;
        $this->statisticsService = $statisticsService;
    }

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
    public function pzc(Request $request): Response
    {
        if ($this->getUser()) {
            $paramService = new RequestParamService($request);
            $hospitalId = (int) $paramService->getHospital();

            if (!empty($hospitalId)) {
                $hospital = $this->hospitalRepository->findById($hospitalId);

                if ($this->isGranted('viewStats', $hospital)) {
                    $this->allocationQuery->filterByHospital($hospital);
                } else {
                    throw $this->createAccessDeniedException('Cannot access this resource.');
                }
            }
        }

        $this->allocationQuery->groupBy('pzc');
        $allocations = $this->allocationQuery->execute()->hydrateResultsAs(PZCStatisticsDto::class);

        $results = $this->statisticsService->generatePZCResults($allocations);

        return $this->render('statistics/pzc.html.twig', [
            'results' => $results,
        ]);
    }

    #[Route('/statistics/urgency', name: 'app_statistics_urgency')]
    public function urgency(): Response
    {
        return $this->render('statistics/urgency.html.twig', [
        ]);
    }
}
