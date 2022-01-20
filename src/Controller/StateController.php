<?php

namespace App\Controller;

use App\Domain\Command\CreateStateCommand;
use App\Domain\Command\DeleteStateCommand;
use App\Domain\Command\UpdateStateCommand;
use App\Entity\State;
use App\Form\StateType;
use App\Repository\StateRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

class StateController extends AbstractController
{
    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    #[Route('/state', name: 'state')]
    public function index(Request $request, StateRepository $stateRepository): Response
    {
        $states = $stateRepository->findAll();

        $state = new State();
        $form = $this->createForm(StateType::class, $state);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $command = new CreateStateCommand($state->getName());

            $this->messageBus->dispatch($command);

            return $this->redirectToRoute('state');
        }

        return $this->render('state/index.html.twig', [
            'states' => $states,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/state/{id}/edit', name: 'state_edit')]
    public function edit($id, Request $request, StateRepository $stateRepository): Response
    {
        $state = $stateRepository->getById($id);

        $form = $this->createForm(StateType::class, $state);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $command = new UpdateStateCommand($state->getName());

            $this->messageBus->dispatch($command);

            return $this->redirectToRoute('state');
        }

        return $this->render('state/edit.html.twig', [
            'state' => $state,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/state/{id}/delete', name: 'state_delete')]
    public function delete(int $id, StateRepository $stateRepository): Response
    {
        $state = $stateRepository->getById($id);

        $command = new DeleteStateCommand($id);

        $this->messageBus->dispatch($command);

        return $this->redirectToRoute('state');
    }
}
