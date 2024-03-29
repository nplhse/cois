<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Service\CookieConsentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CookieConsentController extends AbstractController
{
    public function __construct(
        private CookieConsentService $consentService
    ) {
    }

    #[Route('/cookie_consent', name: 'app_cookie_consent')]
    public function index(Request $request): Response
    {
        $form = $this->consentService->createForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $response = new RedirectResponse($data['target']);
            $this->consentService->handleFormSubmit($data, $request, $response);
        } else {
            $response = new RedirectResponse('/');
        }

        return $response;
    }
}
