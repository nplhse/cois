<?php

namespace App\Controller;

use App\Domain\Enum\Page\PageTypeEnum;
use App\Form\CookieConsentType;
use App\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    public function __construct(
        private PageRepository $pageRepository
    ) {
    }

    #[Route('/page/{slug}', name: 'app_page')]
    public function index(string $slug): Response
    {
        $page = $this->pageRepository->findOneBy(['slug' => $slug]);

        if (null === $page) {
            throw new NotFoundHttpException(sprintf('Page %s could not be found', $slug));
        }

        $this->denyAccessUnlessGranted('view', $page);

        if (PageTypeEnum::PrivacyPage === $page->getType()) {
            $form = $this->createForm(CookieConsentType::class);
        } else {
            $form = null;
        }

        return $this->renderForm('page/index.html.twig', [
            'page' => $page,
            'consentForm' => $form,
        ]);
    }
}
