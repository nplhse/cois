<?php

namespace App\Controller\Settings;

use App\Factory\OrderFilterFactory;
use App\Factory\PaginationFactory;
use App\Factory\SearchFilterFactory;
use App\Repository\CookieConsentRepository;
use App\Repository\HospitalRepository;
use App\Service\Filters\OrderFilter;
use App\Service\Filters\PageFilter;
use App\Service\Filters\SearchFilter;
use App\Service\FilterService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_ADMIN')]
class CookieConsentController extends AbstractController
{
    private FilterService $filterService;

    public function __construct(FilterService $filterService)
    {
        $this->filterService = $filterService;
    }

    #[Route('/settings/cookie_consent/', name: 'app_settings_cookie_consent')]
    public function index(Request $request, CookieConsentRepository $consentRepository, OrderFilterFactory $orderFilterFactory, SearchFilterFactory $searchFilterFactory): Response
    {
        $this->filterService->setRequest($request);
        $this->filterService->configureFilters([PageFilter::Param, OrderFilter::Param, SearchFilter::Param]);

        $paginator = $consentRepository->getPaginator($this->filterService);

        $orderFilterFactory->setHiddenFields([
            SearchFilter::Param => $this->filterService->getValue(SearchFilter::Param),
        ]);

        $sortForm = $orderFilterFactory->setSortable(HospitalRepository::SORTABLE)->setAction($this->generateUrl('app_settings_hospital_index'))->getForm();

        $searchFilterFactory->setHiddenFields([
            OrderFilter::Param => $this->filterService->getValue(OrderFilter::Param),
        ]);

        $searchForm = $searchFilterFactory->setAction($this->generateUrl('app_settings_cookie_consent'))->getForm();
        $searchForm->handleRequest($request);

        return $this->renderForm('settings/cookie_consent/index.html.twig', [
            'filters' => $this->filterService->getFilterDto(),
            'sortForm' => $sortForm,
            'searchForm' => $searchForm,
            'cookies' => $paginator,
            'pages' => PaginationFactory::create($this->filterService->getValue(PageFilter::Param), count($paginator), CookieConsentRepository::PER_PAGE),
        ]);
    }

    #[Route('/settings/cookie_consent/revoke', name: 'app_settings_cookie_consent_revoke')]
    public function revoke(CookieConsentRepository $consentRepository): Response
    {
        $consentRepository->delete();

        $this->addFlash('success', 'All Cookie Consents have been revoked.');

        return $this->redirectToRoute('app_settings_cookie_consent');
    }
}
