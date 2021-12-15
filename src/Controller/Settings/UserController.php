<?php

namespace App\Controller\Settings;

use App\Entity\User;
use App\Form\UserCreateType;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\MailerService;
use App\Service\RequestParamService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/settings/user')]
class UserController extends AbstractController
{
    private \Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface $passwordHasher;

    private MailerService $mailer;

    public function __construct(UserPasswordHasherInterface $passwordHasher, MailerService $mailer)
    {
        $this->passwordHasher = $passwordHasher;
        $this->mailer = $mailer;
    }

    #[Route('/', name: 'app_settings_user_index', methods: ['GET'])]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $paramService = new RequestParamService($request);

        $filters = [];
        $filters['search'] = $paramService->getSearch();
        $filters['page'] = $paramService->getPage();

        $paginator = $userRepository->getUserPaginator($paramService->getPage(), $filters);

        return $this->render('settings/user/index.html.twig', [
            'users' => $paginator,
            'search' => $filters['search'],
            'filters' => $filters,
            'pages' => $paramService->getPagination(count($paginator), $paramService->getPage(), UserRepository::PAGINATOR_PER_PAGE),
        ]);
    }

    #[Route('/new', name: 'app_settings_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = new User();
        $form = $this->createForm(UserCreateType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPlainPassword()));
            $user->eraseCredentials();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

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
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('settings/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_settings_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($user->getPlainPassword()) {
                $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPlainPassword()));
                $user->eraseCredentials();
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_settings_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('settings/user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_settings_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $CsrfToken = $request->request->get('_token');

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $CsrfToken)) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_settings_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/welcome', name: 'app_settings_user_welcome')]
    public function sendWelcomeEmail(User $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $this->mailer->sendWelcomeEmail($user);

        $this->addFlash('success', 'Welcome E-Mail was successfully sent to '.$user->getUsername().'.');

        return $this->redirectToRoute('app_settings_user_show', ['id' => $user->getId()]);
    }

    #[Route('/{id}/toggle', name: 'app_settings_user_toggle')]
    public function toggleOption(User $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return new JsonResponse([]);
    }
}
