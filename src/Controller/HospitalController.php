<?php

namespace App\Controller;

use App\Entity\Hospital;
use App\Form\HospitalType;
use App\Repository\AllocationRepository;
use App\Repository\HospitalRepository;
use App\Service\AdminNotificationService;
use App\Service\RequestParamService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/hospitals')]
class HospitalController extends AbstractController
{
    private RequestParamService $paramService;

    private AdminNotificationService $adminNotifier;

    private Security $security;

    public function __construct(AdminNotificationService $adminNotifier, Security $security)
    {
        $this->adminNotifier = $adminNotifier;
        $this->security = $security;
    }

    #[Route('/', name: 'app_hospital_index')]
    public function index(Request $request, HospitalRepository $hospitalRepository): Response
    {
        $paramService = new RequestParamService($request);

        $filters = [];
        $filters['search'] = $paramService->getSearch();
        $filters['page'] = $paramService->getPage();

        $filters['location'] = $paramService->getLocation();
        $filters['size'] = $paramService->getSize();
        $filters['supplyArea'] = $paramService->getSupplyArea();
        $filters['dispatchArea'] = $paramService->getDispatchArea();

        $filters['sortBy'] = $paramService->getSortBy();

        if (!$paramService->getSortBy()) {
            $filters['orderBy'] = 'asc';
        } else {
            $filters['orderBy'] = $paramService->getOrderBy();
        }

        $paginator = $hospitalRepository->getHospitalPaginator($paramService->getPage(), $filters);

        return $this->render('hospitals/index.html.twig', [
            'hospitals' => $paginator,
            'pages' => $paramService->getPagination(count($paginator), $paramService->getPage(), HospitalRepository::PAGINATOR_PER_PAGE),
            'filters' => $filters,
            'filterIsSet' => $paramService->isFilterIsSet(),
            'locations' => $this->getLocations(),
            'sizes' => $this->getSizes(),
            'supplyAreas' => $hospitalRepository->getSupplyAreas(),
            'dispatchAreas' => $hospitalRepository->getDispatchAreas(),
        ]);
    }

    /**
     * @Route("/new", name="app_hospital_new", methods={"GET","POST"})
     */
    public function new(Request $request, HospitalRepository $hospitalRepository): Response
    {
        if (null !== $this->getUser()->getHospital()) {
            throw $this->createAccessDeniedException('You cannot create another hospital.');
        }

        $hospital = new Hospital();

        $form = $this->createForm(HospitalType::class, $hospital);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hospital->setCreatedAt(new \DateTime('NOW'));
            $hospital->setUpdatedAt(new \DateTime('NOW'));
            $hospital->setOwner($this->getUser());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($hospital);
            $entityManager->flush();

            $this->addFlash('success', 'Your hospital was successfully created.');

            $this->adminNotifier->sendNewHospitalNotification($hospital);

            return $this->redirectToRoute('app_hospital_show', ['id' => $hospital->getId()]);
        }

        return $this->render('hospitals/new.html.twig', [
            'form' => $form->createView(),
            'hospital' => $hospital,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="app_hospital_edit", methods={"GET","POST"})
     */
    public function edit(Hospital $hospital, Request $request, HospitalRepository $hospitalRepository): Response
    {
        $this->denyAccessUnlessGranted('edit', $hospital);

        $form = $this->createForm(HospitalType::class, $hospital);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hospital->setUpdatedAt(new \DateTime('NOW'));

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Hospital was successfully edited.');

            return $this->redirectToRoute('app_hospital_show', ['id' => $hospital->getId()]);
        }

        return $this->render('hospitals/edit.html.twig', [
            'form' => $form->createView(),
            'hospital' => $hospital,
        ]);
    }

    /**
     * @Route("/{id}", name="app_hospital_show", methods={"GET"})
     */
    public function show(Hospital $hospital, AllocationRepository $allocationRepository): Response
    {
        $userIsOwner = $hospital->getOwner() == $this->getUser();

        if ($this->security->isGranted('ROLE_ADMIN')) {
            $userIsOwner = true;
        }

        return $this->render('hospitals/show.html.twig', [
            'hospital' => $hospital,
            'hospital_allocations' => $allocationRepository->countAllocations($hospital),
            'user_can_edit' => true,
        ]);
    }

    private function getLocations(): array
    {
        return [
            [
                'element' => 'rural',
            ],
            [
                'element' => 'urban',
            ],
        ];
    }

    private function getSizes(): array
    {
        return [
            [
                'element' => 'small',
            ],
            [
                'element' => 'medium',
            ],
            [
                'element' => 'large',
            ],
        ];
    }
}
