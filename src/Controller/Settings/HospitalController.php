<?php

namespace App\Controller\Settings;

use App\Domain\Command\Hospital\CreateHospitalCommand;
use App\Domain\Command\Hospital\DeleteHospitalCommand;
use App\Domain\Command\Hospital\EditHospitalCommand;
use App\Domain\Contracts\HospitalInterface;
use App\Entity\Hospital;
use App\Form\HospitalType;
use App\Repository\HospitalRepository;
use App\Service\RequestParamService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_ADMIN')]
#[Route('/settings/hospital')]
class HospitalController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    private MessageBusInterface $messageBus;

    public function __construct(EntityManagerInterface $entityManager, MessageBusInterface $messageBus)
    {
        $this->entityManager = $entityManager;
        $this->messageBus = $messageBus;
    }

    #[Route('/', name: 'app_settings_hospital_index', methods: ['GET'])]
    public function index(Request $request, HospitalRepository $hospitalRepository): Response
    {
        $paramService = new RequestParamService($request);

        $filters = [];
        $filters['search'] = $paramService->getSearch();
        $filters['page'] = $paramService->getPage();

        $filters['hospital'] = $paramService->getHospital();
        $filters['supplyArea'] = $paramService->getSupplyArea();
        $filters['dispatchArea'] = $paramService->getDispatchArea();
        $filters['location'] = $paramService->getLocation();
        $filters['size'] = $paramService->getSize();

        $paginator = $hospitalRepository->getHospitalPaginator($paramService->getPage());

        return $this->render('settings/hospital/index.html.twig', [
            'hospitals' => $paginator,
            'search' => $filters['search'],
            'filters' => $filters,
            'pages' => $paramService->getPagination(count($paginator), $paramService->getPage(), HospitalRepository::PAGINATOR_PER_PAGE),
        ]);
    }

    #[Route('/new', name: 'app_settings_hospital_new', methods: ['GET', 'POST'])]
    public function new(Request $request, HospitalRepository $hospitalRepository): Response
    {
        $hospital = new Hospital();

        $form = $this->createForm(HospitalType::class, $hospital, [
            'backend' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $command = new CreateHospitalCommand(
                $hospital->getOwner(),
                $hospital->getName(),
                $hospital->getAddress(),
                $hospital->getState(),
                $hospital->getDispatchArea(),
                $hospital->getSupplyArea(),
                $hospital->getLocation(),
                $hospital->getBeds(),
                $hospital->getSize()
            );

            try {
                $this->messageBus->dispatch($command);
            } catch (HandlerFailedException) {
                $this->addFlash('danger', 'Sorry, something went wrong. Please try again later!');
            }

            /** @var ?HospitalInterface $hospital */
            $hospital = $hospitalRepository->findOneByTriplet($command->getName(), $command->getLocation(), $command->getBeds());

            if (null === $hospital) {
                throw new \RuntimeException('Sorry, something went wrong. Please try again later.');
            }

            return $this->redirectToRoute('app_settings_hospital_show', ['id' => $hospital->getId()]);
        }

        return $this->renderForm('settings/hospital/new.html.twig', [
            'hospital' => $hospital,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_settings_hospital_show', methods: ['GET'])]
    public function show(Hospital $hospital): Response
    {
        return $this->render('settings/hospital/show.html.twig', [
            'hospital' => $hospital,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_settings_hospital_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Hospital $hospital): Response
    {
        $form = $this->createForm(HospitalType::class, $hospital, ['backend' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $command = new EditHospitalCommand(
                $hospital->getId(),
                $hospital->getOwner(),
                $hospital->getName(),
                $hospital->getAddress(),
                $hospital->getState(),
                $hospital->getDispatchArea(),
                $hospital->getSupplyArea(),
                $hospital->getLocation(),
                $hospital->getBeds(),
                $hospital->getSize()
            );

            try {
                $this->messageBus->dispatch($command);
            } catch (HandlerFailedException) {
                $this->addFlash('danger', 'Sorry, something went wrong. Please try again later!');

                return $this->renderForm('settings/hospital/edit.html.twig', [
                    'hospital' => $hospital,
                    'form' => $form,
                ]);
            }

            return $this->redirectToRoute('app_settings_hospital_edit', ['id' => $hospital->getId()], Response::HTTP_SEE_OTHER);
        }

        $this->addFlash('success', 'Hospital '.$hospital->getName().' has been edited.');

        return $this->renderForm('settings/hospital/edit.html.twig', [
            'hospital' => $hospital,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_settings_hospital_delete', methods: ['POST'])]
    public function delete(Request $request, Hospital $hospital): Response
    {
        $CsrfToken = $request->request->get('_token');

        if ($this->isCsrfTokenValid('delete'.$hospital->getId(), $CsrfToken)) {
            $command = new DeleteHospitalCommand($hospital->getId());

            try {
                $this->messageBus->dispatch($command);
            } catch (HandlerFailedException) {
                $this->addFlash('danger', 'Sorry, something went wrong. Please try again later!');
            }

            $this->addFlash('success', 'Hospital '.$hospital->getName().' has been deleted.');
        }

        return $this->redirectToRoute('app_settings_hospital_index', [], Response::HTTP_SEE_OTHER);
    }
}
