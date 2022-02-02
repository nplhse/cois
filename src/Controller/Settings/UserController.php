<?php

namespace App\Controller\Settings;

use App\Entity\User;
use App\Form\User\UserCreateType;
use App\Form\User\UserType;
use App\Repository\UserRepository;
use App\Service\Filters\OrderFilter;
use App\Service\Filters\PageFilter;
use App\Service\Filters\SearchFilter;
use App\Service\FilterService;
use App\Service\MailerService;
use App\Service\PaginationFactory;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_ADMIN')]
#[Route('/settings/user')]
class UserController extends AbstractController
{
    private UserPasswordHasherInterface $passwordHasher;

    private EntityManagerInterface $entityManager;

    private FilterService $filterService;

    public function __construct(FilterService $filterService, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager)
    {
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
        $this->filterService = $filterService;
    }

    #[Route('/', name: 'app_settings_user_index', methods: ['GET'])]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        $this->filterService->setRequest($request);
        $this->filterService->configureFilters([PageFilter::Param, SearchFilter::Param, OrderFilter::Param]);

        $paginator = $userRepository->getUserPaginator($this->filterService);

        return $this->render('settings/user/index.html.twig', [
            'filters' => $this->filterService->getFilterDto(),
            'search' => $this->filterService->getValue(SearchFilter::Param),
            'users' => $paginator,
            'pages' => PaginationFactory::create($this->filterService->getValue(PageFilter::Param), count($paginator), UserRepository::PER_PAGE),
        ]);
    }

    #[Route('/new', name: 'app_settings_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(UserCreateType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPlainPassword()));
            $user->eraseCredentials();

            $userRepository->add($user);

            return $this->redirectToRoute('app_settings_user_index', [], Response::HTTP_SEE_OTHER);
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
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($user->getPlainPassword()) {
                $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPlainPassword()));
                $user->eraseCredentials();
            }

            $userRepository->save();
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_settings_user_index', [], Response::HTTP_SEE_OTHER);
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

    #[Route('/{id}/welcome', name: 'app_settings_user_welcome')]
    public function sendWelcomeEmail(User $user, MailerService $mailer): Response
    {
        try {
            $mailer->sendWelcomeEmail($user);
        } catch (\Exception $e) {
            $this->addFlash('danger', 'Could not send welcome E-Mail to '.$user->getUsername().'.');
            return $this->redirectToRoute('app_settings_user_show', ['id' => $user->getId()]);
        }

        $this->addFlash('success', 'Welcome E-Mail was successfully sent to '.$user->getUsername().'.');

        return $this->redirectToRoute('app_settings_user_show', ['id' => $user->getId()]);
    }

    // Todo: REMOVE AFTER REFACTORING
    #[Route('/{id}/toggle', name: 'app_settings_user_toggle')]
    public function toggleOption(User $user): Response
    {
        return new JsonResponse([]);
    }
}
