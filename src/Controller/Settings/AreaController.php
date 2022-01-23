<?php

namespace App\Controller\Settings;

use App\Application\Exception\StateNotEmptyException;
use App\Domain\Command\CreateDispatchAreaCommand;
use App\Domain\Command\CreateStateCommand;
use App\Domain\Command\DeleteDispatchAreaCommand;
use App\Domain\Command\DeleteStateCommand;
use App\Domain\Command\SupplyArea\CreateSupplyAreaCommand;
use App\Domain\Command\SupplyArea\DeleteSupplyAreaCommand;
use App\Domain\Command\SupplyArea\SwitchStateSupplyAreaCommand;
use App\Domain\Command\SupplyArea\UpdateSupplyAreaCommand;
use App\Domain\Command\SwitchStateDispatchAreaCommand;
use App\Domain\Command\UpdateDispatchAreaCommand;
use App\Domain\Command\UpdateStateCommand;
use App\Entity\DispatchArea;
use App\Entity\State;
use App\Entity\SupplyArea;
use App\Form\DispatchAreaType;
use App\Form\StateType;
use App\Form\SupplyAreaType;
use App\Repository\DispatchAreaRepository;
use App\Repository\StateRepository;
use App\Repository\SupplyAreaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/settings/area')]
class AreaController extends AbstractController
{
    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    #[Route('/', name: 'app_settings_area_index')]
    public function index(Request $request, StateRepository $stateRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $states = $stateRepository->findAll();

        return $this->render('settings/area/index.html.twig', [
            'states' => $states,
        ]);
    }

    #[Route('/state/new', name: 'app_settings_area_state_new')]
    public function state_new(Request $request, StateRepository $stateRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $state = new State();
        $form = $this->createForm(StateType::class, $state);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $command = new CreateStateCommand($state->getName());

            $this->messageBus->dispatch($command);

            return $this->redirectToRoute('app_settings_area_index');
        }

        return $this->render('settings/area/state/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/state/{id}/edit', name: 'app_settings_area_state_edit')]
    public function state_edit($id, Request $request, StateRepository $stateRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $state = $stateRepository->getById($id);

        $form = $this->createForm(StateType::class, $state);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $command = new UpdateStateCommand($state->getId(), $state->getName());

            $this->messageBus->dispatch($command);

            return $this->redirectToRoute('app_settings_area_index');
        }

        return $this->render('settings/area/state/edit.html.twig', [
            'state' => $state,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/state/{id}/delete', name: 'app_settings_area_state_delete')]
    public function state_delete(int $id, StateRepository $stateRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $state = $stateRepository->getById($id);

        $command = new DeleteStateCommand($id);

        try {
            $this->messageBus->dispatch($command);
        } catch (HandlerFailedException $exception) {
            $this->addFlash('danger', 'Could not delete State: '.$state->getName().'. Maybe it still contains Dispatch Areas?');
        }

        return $this->redirectToRoute('app_settings_area_index');
    }

    #[Route('/dispatch_area/new', name: 'app_settings_area_dispatch_new')]
    public function dispatch_new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $area = new DispatchArea();
        $form = $this->createForm(DispatchAreaType::class, $area);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $command = new CreateDispatchAreaCommand($area->getName(), $area->getState());

            $this->messageBus->dispatch($command);

            return $this->redirectToRoute('app_settings_area_index');
        }

        return $this->render('settings/area/dispatch_area/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/dispatch_area/{id}/edit', name: 'app_settings_area_dispatch_edit')]
    public function dispatch_edit(int $id, Request $request, DispatchAreaRepository $dispatchAreaRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $area = $dispatchAreaRepository->getById($id);

        $previousState = $area->getState();

        $form = $this->createForm(DispatchAreaType::class, $area);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $command = new UpdateDispatchAreaCommand($area->getId(), $area->getName());

            $this->messageBus->dispatch($command);

            if ($previousState !== $area->getState()) {
                $command = new SwitchStateDispatchAreaCommand($area->getId(), $area->getState()->getId());

                $this->messageBus->dispatch($command);
            }

            return $this->redirectToRoute('app_settings_area_index');
        }

        return $this->render('settings/area/dispatch_area/edit.html.twig', [
            'area' => $area,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/dispatch_area/{id}/delete', name: 'app_settings_area_dispatch_delete')]
    public function dispatch_delete(int $id, DispatchAreaRepository $dispatchAreaRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $area = $dispatchAreaRepository->getById($id);

        $command = new DeleteDispatchAreaCommand($id);

        $this->messageBus->dispatch($command);

        return $this->redirectToRoute('app_settings_area_index');
    }

    #[Route('/supply_area/new', name: 'app_settings_area_supply_new')]
    public function supply_new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $area = new SupplyArea();
        $form = $this->createForm(SupplyAreaType::class, $area);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $command = new CreateSupplyAreaCommand($area->getName(), $area->getState()->getId());

            $this->messageBus->dispatch($command);

            return $this->redirectToRoute('app_settings_area_index');
        }

        return $this->render('settings/area/supply_area/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/supply_area/{id}/delete', name: 'app_settings_area_supply_delete')]
    public function supply_delete(int $id, SupplyAreaRepository $supplyAreaRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $area = $supplyAreaRepository->getById($id);

        $command = new DeleteSupplyAreaCommand($id);

        $this->messageBus->dispatch($command);

        return $this->redirectToRoute('app_settings_area_index');
    }

    #[Route('/supply_area/{id}/edit', name: 'app_settings_area_supply_edit')]
    public function supply_edit(int $id, Request $request, SupplyAreaRepository $supplyAreaRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $area = $supplyAreaRepository->getById($id);

        $previousState = $area->getState();

        $form = $this->createForm(SupplyAreaType::class, $area);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $command = new UpdateSupplyAreaCommand($area->getId(), $area->getName());

            $this->messageBus->dispatch($command);

            if ($previousState !== $area->getState()) {
                $command = new SwitchStateSupplyAreaCommand($area->getId(), $area->getState()->getId());

                $this->messageBus->dispatch($command);
            }

            return $this->redirectToRoute('app_settings_area_index');
        }

        return $this->render('settings/area/supply_area/edit.html.twig', [
            'area' => $area,
            'form' => $form->createView(),
        ]);
    }
}
