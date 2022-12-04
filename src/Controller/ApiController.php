<?php

declare(strict_types=1);

namespace App\Controller;

use App\DataTransferObjects\DayStatisticsDto;
use App\DataTransferObjects\GenderStatisticsDto;
use App\DataTransferObjects\PropertyStatisticsDto;
use App\DataTransferObjects\PZCStatisticsDto;
use App\DataTransferObjects\TimeStatisticsDto;
use App\DataTransferObjects\TransportStatisticsDto;
use App\DataTransferObjects\UrgencyStatisticsDto;
use App\Entity\Hospital;
use App\Query\AllocationAgeQuery;
use App\Query\AllocationPropertyQuery;
use App\Query\AllocationQuery;
use App\Repository\HospitalRepository;
use App\Service\RequestParamService;
use App\Service\StatisticsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    private ?Hospital $hospital = null;

    public function __construct(
        private HospitalRepository $hospitalRepository,
        private AllocationQuery $allocationQuery,
        private StatisticsService $statisticsService
    ) {
    }

    #[Route('/api/age.json', name: 'app_api_age')]
    public function age(Request $request, AllocationAgeQuery $query): Response
    {
        $this->filterByHospital($request);

        $allocations = $query->execute();
        $result = $this->statisticsService->generateAgeResults($allocations);

        return new JsonResponse($result);
    }

    #[Route('/api/days.json', name: 'app_api_days')]
    public function days(Request $request): Response
    {
        $this->filterByHospital($request);

        $this->allocationQuery->groupBy('days');
        $allocations = $this->allocationQuery->execute()->hydrateResultsAs(DayStatisticsDto::class);

        $results = $this->statisticsService->generateDayResults($allocations);

        return new JsonResponse($results);
    }

    #[Route('/api/gender.json', name: 'app_api_gender')]
    public function gender(Request $request): Response
    {
        $this->filterByHospital($request);

        $this->allocationQuery->groupBy('gender');
        $allocations = $this->allocationQuery->execute()->hydrateResultsAs(GenderStatisticsDto::class);

        $results = $this->statisticsService->generateGenderResults($allocations);

        return new JsonResponse($results);
    }

    #[Route('/api/times.json', name: 'app_data_time')]
    public function times(Request $request): Response
    {
        $this->filterByHospital($request);

        $this->allocationQuery->groupBy('times');
        $allocations = $this->allocationQuery->execute()->hydrateResultsAs(TimeStatisticsDto::class);

        $results = $this->statisticsService->generateTimeResults($allocations);

        return new JsonResponse($results);
    }

    #[Route('/api/transport.json', name: 'app_api_transport')]
    public function transport(Request $request): Response
    {
        $this->filterByHospital($request);

        $this->allocationQuery->groupBy('transport');
        $allocations = $this->allocationQuery->execute()->hydrateResultsAs(TransportStatisticsDto::class);

        $results = $this->statisticsService->generateTransportResults($allocations);

        return new JsonResponse($results);
    }

    #[Route('/api/pzc.json', name: 'app_data_pzc')]
    public function pzc(Request $request): Response
    {
        $this->filterByHospital($request);

        $this->allocationQuery->groupBy('pzc');
        $allocations = $this->allocationQuery->execute()->hydrateResultsAs(PZCStatisticsDto::class);

        $results = $this->statisticsService->generatePZCResults($allocations);

        return new JsonResponse($results);
    }

    #[Route('/api/urgency.json', name: 'app_api_urgency')]
    public function urgency(Request $request): Response
    {
        $this->filterByHospital($request);

        $this->allocationQuery->groupBy('urgency');
        $allocations = $this->allocationQuery->execute()->hydrateResultsAs(UrgencyStatisticsDto::class);

        $results = $this->statisticsService->generateUrgencyResults($allocations);

        return new JsonResponse($results);
    }

    #[Route('/api/property/{target}.json', name: 'app_api_property')]
    public function property(string $target, Request $request, AllocationPropertyQuery $query): Response
    {
        $properties = ['requiresResus', 'requiresCathlab', 'isCPR', 'isVentilated', 'isShock', 'isPregnant', 'isWithPhysician', 'isWorkAccident'];

        if (!in_array($target, $properties, true)) {
            throw $this->createNotFoundException('Cannot access this resource.');
        }

        if ($this->filterByHospital($request)) {
            $query->filterByHospital($this->hospital);
        }

        $query->setTargetProoperty($target);

        $allocations = $query->execute()->hydrateResultsAs(PropertyStatisticsDto::class);
        $result = $this->statisticsService->generatePropertyResults($allocations, $target);

        return new JsonResponse($result);
    }

    private function filterByHospital(Request $request): bool
    {
        if ($this->getUser()) {
            $paramService = new RequestParamService($request);
            $hospitalId = (int) $paramService->getHospital();

            if (!empty($hospitalId)) {
                /* @phpstan-ignore-next-line */
                $this->hospital = $this->hospitalRepository->findById($hospitalId);

                if ($this->isGranted('viewStats', $this->hospital)) {
                    $this->allocationQuery->filterByHospital($this->hospital);

                    return true;
                } else {
                    throw $this->createAccessDeniedException('Cannot access this resource.');
                }
            }
        }

        return false;
    }
}
