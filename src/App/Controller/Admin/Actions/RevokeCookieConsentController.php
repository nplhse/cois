<?php

declare(strict_types=1);

namespace App\Controller\Admin\Actions;

use App\Controller\Admin\CookieConsentCrudController;
use App\Repository\CookieConsentRepository;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/cookies/revoke', name: 'admin_cookies_revoke')]
class RevokeCookieConsentController extends AbstractController
{
    public function __construct(
        private CookieConsentRepository $consentRepository,
        private AdminUrlGenerator $adminUrlGenerator
    ) {
    }

    public function __invoke(): Response
    {
        $this->consentRepository->delete();

        $this->addFlash('success', 'All Cookie Consents have been revoked.');

        return $this->redirect($this->adminUrlGenerator->setController(CookieConsentCrudController::class)
            ->setAction('index')
            ->generateUrl());
    }
}
