<?php

namespace App\Controller\Settings;

use App\Domain\Command\Hospital\CreateHospitalCommand;
use App\Domain\Command\Hospital\DeleteHospitalCommand;
use App\Domain\Command\Hospital\EditHospitalCommand;
use App\Domain\Contracts\HospitalInterface;
use App\Entity\Hospital;
use App\Factory\HospitalFilterFactory;
use App\Factory\OrderFilterFactory;
use App\Factory\PaginationFactory;
use App\Factory\SearchFilterFactory;
use App\Form\HospitalType;
use App\Repository\HospitalRepository;
use App\Service\Filters\DispatchAreaFilter;
use App\Service\Filters\LocationFilter;
use App\Service\Filters\OrderFilter;
use App\Service\Filters\PageFilter;
use App\Service\Filters\SearchFilter;
use App\Service\Filters\SizeFilter;
use App\Service\Filters\StateFilter;
use App\Service\Filters\SupplyAreaFilter;
use App\Service\FilterService;
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
    private FilterService $filterService;

    private MessageBusInterface $messageBus;

    public function __construct(FilterService $filterService, MessageBusInterface $messageBus)
    {
        $this->filterService = $filterService;
        $this->messageBus = $messageBus;
    }

    #[Route('/', name: 'app_settings_hospital_index', methods: ['GET'])]
    public function index(Request $request, HospitalRepository $hospitalRepository, HospitalFilterFactory $hospitalFilterFactory, OrderFilterFactory $orderFilterFactory, SearchFilterFactory $searchFilterFactory): Response
    {
        $this->filterService->setRequest($request);
        $this->filterService->configureFilters($hospitalFilterFactory->getFilters());

        $paginator = $hospitalRepository->getHospitalPaginator($this->filterService);

        $hospitalFilterFactory->setHiddenFields([
            SearchFilter::Param => $this->filterService->getValue(SearchFilter::Param),
            OrderFilter::Param => $this->filterService->getValue(OrderFilter::Param),
        ]);

        $hospitalForm = $hospitalFilterFactory->setAction($this->generateUrl('app_settings_hospital_index'))->getForm();
        $hospitalForm->handleRequest($request);

        $orderFilterFactory->setHiddenFields([
            LocationFilter::Param => $this->filterService->getValue(LocationFilter::Param),
            SizeFilter::Param => $this->filterService->getValue(SizeFilter::Param),
            StateFilter::Param => $this->filterService->getValue(StateFilter::Param),
            SupplyAreaFilter::Param => $this->filterService->getValue(SupplyAreaFilter::Param),
            DispatchAreaFilter::Param => $this->filterService->getValue(DispatchAreaFilter::Param),
            SearchFilter::Param => $this->filterService->getValue(SearchFilter::Param),
        ]);

        $sortForm = $orderFilterFactory->setSortable(HospitalRepository::SORTABLE)->setAction($this->generateUrl('app_settings_hospital_index'))->getForm();

        $searchFilterFactory->setHiddenFields([
            OrderFilter::Param => $this->filterService->getValue(OrderFilter::Param),
        ]);

        $searchForm = $searchFilterFactory->setAction($this->generateUrl('app_settings_hospital_index'))->getForm();
        $searchForm->handleRequest($request);

        return $this->renderForm('settings/hospital/index.html.twig', [
            'filters' => $this->filterService->getFilterDto(),
            'sortForm' => $sortForm,
            'searchForm' => $searchForm,
            'hospitalForm' => $hospitalForm,
            'hospitals' => $paginator,
            'pages' => PaginationFactory::create($this->filterService->getValue(PageFilter::Param), count($paginator), HospitalRepository::PER_PAGE),
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
