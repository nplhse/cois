<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Query\TracerDiagnosisByQuarterQuery;
use App\Query\UrgencyBySizeQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PosterApiController extends AbstractController
{
    public function __construct(
        private TracerDiagnosisByQuarterQuery $query,
        private UrgencyBySizeQuery $urgencyBySizeQuery,
    ) {
    }

    #[Route('/api/tracer_diagnosis_by_quarter.json', name: 'app_api_tracer_by_quarter')]
    public function tracer_diagnosis_by_quarter(): Response
    {
        $result = [];

        $result['total'] = $this->query->execute('total')->getItems();
        $result['cpr'] = $this->query->execute('cpr')->getItems();
        $result['pulmonary_embolism'] = $this->query->execute('pulmonary_embolism')->getItems();
        $result['pneumonia_copd'] = $this->query->execute('pneumonia_copd')->getItems();
        $result['stemi_acs'] = $this->query->execute('stemi_acs')->getItems();
        $result['stroke'] = $this->query->execute('stroke')->getItems();

        return $this->json($result);
    }

    #[Route('/api/urgency_by_size.json', name: 'app_urgency_by_size')]
    public function urgency_by_size(): Response
    {
        $result = $this->urgencyBySizeQuery->execute()->getItems();

        return $this->json($result);
    }
}
