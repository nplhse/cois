<?php

namespace App\Controller\Settings;

use App\Domain\Command\User\CreateUserCommand;
use App\Domain\Command\User\EditUserCommand;
use App\Domain\Command\User\PromoteUserCommand;
use App\Entity\User;
use App\Factory\OrderFilterFactory;
use App\Factory\PaginationFactory;
use App\Factory\SearchFilterFactory;
use App\Form\User\UserType;
use App\Repository\UserRepository;
use App\Service\Filters\OrderFilter;
use App\Service\Filters\PageFilter;
use App\Service\Filters\SearchFilter;
use App\Service\FilterService;
use App\Service\MailerService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_ADMIN')]
#[Route('/settings/user')]
class UserController extends AbstractController
{
    private FilterService $filterService;

    private MessageBusInterface $messageBus;

    public function __construct(FilterService $filterService, MessageBusInterface $messageBus)
    {
        $this->filterService = $filterService;
        $this->messageBus = $messageBus;
    }

    #[Route('/', name: 'app_settings_user_index', methods: ['GET'])]
    public function index(Request $request, UserRepository $userRepository, OrderFilterFactory $orderFilterFactory, SearchFilterFactory $searchFilterFactory): Response
    {
        $this->filterService->setRequest($request);
        $this->filterService->configureFilters([PageFilter::Param, SearchFilter::Param, OrderFilter::Param]);

        $paginator = $userRepository->getUserPaginator($this->filterService);

        $orderFilterFactory->setHiddenFields([
            SearchFilter::Param => $this->filterService->getValue(SearchFilter::Param),
        ]);

        $sortForm = $orderFilterFactory->setSortable(UserRepository::SORTABLE)->setAction($this->generateUrl('app_settings_user_index'))->getForm();

        $searchFilterFactory->setHiddenFields([
            OrderFilter::Param => $this->filterService->getValue(OrderFilter::Param),
        ]);

        $searchForm = $searchFilterFactory->setAction($this->generateUrl('app_settings_user_index'))->getForm();
        $searchForm->handleRequest($request);

        return $this->renderForm('settings/user/index.html.twig', [
            'filters' => $this->filterService->getFilterDto(),
            'sortForm' => $sortForm,
            'searchForm' => $searchForm,
            'users' => $paginator,
            'pages' => PaginationFactory::create($this->filterService->getValue(PageFilter::Param), count($paginator), UserRepository::PER_PAGE),
        ]);
    }

    #[Route('/new', name: 'app_settings_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user, [
            'creation' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $command = new CreateUserCommand(
                $user->getUsername(),
                $user->getEmail(),
                $user->getPlainPassword(),
                $user->getRoles(),
                false,
                false,
                false
            );

            try {
                $this->messageBus->dispatch($command);
            } catch (HandlerFailedException) {
                $this->addFlash('danger', 'Sorry, something went wrong. Please try again later!');

                return $this->renderForm('settings/user/new.html.twig', [
                    'user' => $user,
                    'form' => $form,
                ]);
            }

            $this->addFlash('success', 'New user has been created!');

            return $this->redirectToRoute('app_settings_user_index');
        }

        return $this->renderForm('settings/user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_settings_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('settings/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_settings_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $command = new EditUserCommand(
                $user->getId(),
                $user->getUsername(),
                $user->getEmail(),
                $user->getPlainPassword(),
                $user->getRoles()
            );

            try {
                $this->messageBus->dispatch($command);
            } catch (HandlerFailedException) {
                $this->addFlash('danger', 'Sorry, something went wrong. Please try again later!');

                return $this->renderForm('settings/user/edit.html.twig', [
                    'user' => $user,
                    'form' => $form,
                ]);
            }

            $this->addFlash('success', 'New user has been created!');

            return $this->redirectToRoute('app_settings_user_show', ['id' => $user->getId()]);
        }

        return $this->renderForm('settings/user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_settings_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        $CsrfToken = $request->request->get('_token');

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $CsrfToken)) {
            $userRepository->delete($user);
        }

        return $this->redirectToRoute('app_settings_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/promote/{action}', name: 'app_settings_user_verify')]
    public function promote(User $user, string $action): Response
    {
        if ('verify' === $action) {
            $isVerified = true;
            $isParticipant = $user->isParticipant();
        } elseif ('unverify' === $action) {
            $isVerified = false;
            $isParticipant = $user->isParticipant();
        } elseif ('enable' === $action) {
            $isVerified = $user->isVerified();
            $isParticipant = true;
        } elseif ('disable' === $action) {
            $isVerified = $user->isVerified();
            $isParticipant = false;
        } else {
            $isVerified = $user->isVerified();
            $isParticipant = $user->isParticipant();
        }

        $command = new PromoteUserCommand(
            $user->getId(),
            $isVerified,
            $isParticipant
        );

        try {
            $this->messageBus->dispatch($command);
        } catch (HandlerFailedException) {
            $this->addFlash('danger', 'Sorry, something went wrong. Please try again later!');
        }

        $this->addFlash('success', 'User has been promoted.');

        return $this->redirectToRoute('app_settings_user_show', ['id' => $user->getId()]);
    }

    #[Route('/{id}/welcome', name: 'app_settings_user_welcome')]
    public function sendWelcomeEmail(User $user, MailerService $mailer): Response
    {
        try {
            $mailer->sendWelcomeEmail($user);
        } catch (\Exception) {
            $this->addFlash('danger', 'Could not send welcome E-Mail to '.$user->getUsername().'.');

            return $this->redirectToRoute('app_settings_user_show', ['id' => $user->getId()]);
        }

        $this->addFlash('success', 'Welcome E-Mail was successfully sent to '.$user->getUsername().'.');

        return $this->redirectToRoute('app_settings_user_show', ['id' => $user->getId()]);
    }
}
