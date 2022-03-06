<?php

namespace App\Controller;

use App\DataTransferObjects\AssignmentStatisticsDto;
use App\DataTransferObjects\InfectionStatisticsDto;
use App\DataTransferObjects\OccasionStatisticsDto;
use App\DataTransferObjects\PZCStatisticsDto;
use App\DataTransferObjects\SpecialityStatisticsDto;
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

    #[Route('/statistics/age', name: 'app_statistics_age')]
    public function age(): Response
    {
        return $this->render('statistics/age.html.twig', [
        ]);
    }

    #[Route('/statistics/gender', name: 'app_statistics_gender')]
    public function gender(): Response
    {
        return $this->render('statistics/gender.html.twig', [
        ]);
    }

    #[Route('/statistics/transport', name: 'app_statistics_transport')]
    public function transport(): Response
    {
        return $this->render('statistics/transport.html.twig', [
        ]);
    }

    #[Route('/statistics/properties', name: 'app_statistics_properties')]
    public function properties(): Response
    {
        return $this->render('statistics/properties.html.twig', [
        ]);
    }

    #[Route('/statistics/assignment', name: 'app_statistics_assignment')]
    public function assignment(Request $request): Response
    {
        if ($this->getUser()) {
            $paramService = new RequestParamService($request);
            $hospitalId = (int) $paramService->getHospital();

            if (!empty($hospitalId)) {
                /** @phpstan-ignore-next-line */
                $hospital = $this->hospitalRepository->findById($hospitalId);

                if ($this->isGranted('viewStats', $hospital)) {
                    $this->allocationQuery->filterByHospital($hospital);
                } else {
                    throw $this->createAccessDeniedException('Cannot access this resource.');
                }
            }
        }

        $this->allocationQuery->groupBy('assignment');
        $allocations = $this->allocationQuery->execute()->hydrateResultsAs(AssignmentStatisticsDto::class);

        $results = $this->statisticsService->generateAssignmentResults($allocations);

        return $this->render('statistics/assignment.html.twig', [
            'results' => $results,
        ]);
    }

    #[Route('/statistics/infection', name: 'app_statistics_infection')]
    public function infection(Request $request): Response
    {
        if ($this->getUser()) {
            $paramService = new RequestParamService($request);
            $hospitalId = (int) $paramService->getHospital();

            if (!empty($hospitalId)) {
                /** @phpstan-ignore-next-line */
                $hospital = $this->hospitalRepository->findById($hospitalId);

                if ($this->isGranted('viewStats', $hospital)) {
                    $this->allocationQuery->filterByHospital($hospital);
                } else {
                    throw $this->createAccessDeniedException('Cannot access this resource.');
                }
            }
        }

        $this->allocationQuery->groupBy('infection');
        $allocations = $this->allocationQuery->execute()->hydrateResultsAs(InfectionStatisticsDto::class);

        $results = $this->statisticsService->generateInfectionResults($allocations);

        return $this->render('statistics/infection.html.twig', [
            'results' => $results,
        ]);
    }

    #[Route('/statistics/occasion', name: 'app_statistics_occasion')]
    public function occasion(Request $request): Response
    {
        if ($this->getUser()) {
            $paramService = new RequestParamService($request);
            $hospitalId = (int) $paramService->getHospital();

            if (!empty($hospitalId)) {
                /** @phpstan-ignore-next-line */
                $hospital = $this->hospitalRepository->findById($hospitalId);

                if ($this->isGranted('viewStats', $hospital)) {
                    $this->allocationQuery->filterByHospital($hospital);
                } else {
                    throw $this->createAccessDeniedException('Cannot access this resource.');
                }
            }
        }

        $this->allocationQuery->groupBy('occasion');
        $allocations = $this->allocationQuery->execute()->hydrateResultsAs(OccasionStatisticsDto::class);

        $results = $this->statisticsService->generateOccasionResults($allocations);

        return $this->render('statistics/occasion.html.twig', [
            'results' => $results,
        ]);
    }

    #[Route('/statistics/speciality', name: 'app_statistics_speciality')]
    public function speciality(Request $request): Response
    {
        if ($this->getUser()) {
            $paramService = new RequestParamService($request);
            $hospitalId = (int) $paramService->getHospital();

            if (!empty($hospitalId)) {
                /** @phpstan-ignore-next-line */
                $hospital = $this->hospitalRepository->findById($hospitalId);

                if ($this->isGranted('viewStats', $hospital)) {
                    $this->allocationQuery->filterByHospital($hospital);
                } else {
                    throw $this->createAccessDeniedException('Cannot access this resource.');
                }
            }
        }

        $this->allocationQuery->groupBy('speciality');
        $allocations = $this->allocationQuery->execute()->hydrateResultsAs(SpecialityStatisticsDto::class);

        $specialityResults = $this->statisticsService->generateSpecialityResults($allocations);

        $this->allocationQuery->groupBy('specialityDetail');
        $allocations = $this->allocationQuery->execute()->hydrateResultsAs(SpecialityStatisticsDto::class);

        $specialityDetailResults = $this->statisticsService->generateSpecialityResults($allocations);

        return $this->render('statistics/speciality.html.twig', [
            'specialityResults' => $specialityResults,
            'specialityDetailResults' => $specialityDetailResults,
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
                /** @phpstan-ignore-next-line */
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
}
