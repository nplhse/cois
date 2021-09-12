<?php

namespace App\Controller;

use App\Entity\Allocation;
use App\Repository\HospitalRepository;
use App\Repository\ImportRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/{_locale<%app.supported_locales%>}/allocations")
 * @IsGranted("ROLE_USER")
 */
class AllocationController extends AbstractController
{
    /**
     * @Route("/", name="allocation_index")
     */
    public function index(HospitalRepository $hospitalRepository): Response
    {
        $hospitals = $hospitalRepository->findAll();

        $hospitalList = [];
        $hospitalLink = [];

        foreach ($hospitals as $hospital) {
            $hospitalList['/api/hospitals/'.$hospital->getId()] = $hospital->getName();
            $hospitalLink['/api/hospitals/'.$hospital->getId()] = $this->generateUrl('app_hospital_show', ['id' => $hospital->getId()]);
        }

        return $this->render('allocation/index.html.twig', [
            'hospitals' => json_encode($hospitalList),
            'hospital_links' => json_encode($hospitalLink),
        ]);
    }

    /**
     * @Route("/{id}", name="allocation_show", methods={"GET"})
     */
    public function show(Allocation $allocation, ImportRepository $importRepository): Response
    {
        $importUser = $allocation->getImport()->getUser();
        $importUser->getId();

        return $this->render('allocation/show.html.twig', [
            'allocation' => $allocation,
            'importUser' => $importUser,
        ]);
    }
}
