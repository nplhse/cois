<?php

declare(strict_types=1);

namespace App\Controller\Website;

use App\Form\CookieConsentType;
use App\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PrivacyController extends AbstractController
{
    public function __construct(
        private PageRepository $pageRepository
    ) {
    }

    #[Route(path: [
        'en' => '/privacy',
        'de' => '/privatsphaere',
    ], name: 'app_page_privacy')]
    public function index(): Response
    {
        $page = $this->pageRepository->findOneBy(['slug' => 'privacy']);

        $form = $this->createForm(CookieConsentType::class);

        if (null === $page) {
            throw $this->createNotFoundException('Page could not be found');
        }

        return $this->render('website/page/privacy.html.twig', [
            'page' => $page,
            'consentForm' => $form,
        ]);
    }
}
