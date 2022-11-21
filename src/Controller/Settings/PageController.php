<?php

namespace App\Controller\Settings;

use App\Domain\Command\Page\CreatePageCommand;
use App\Domain\Command\Page\DeletePageCommand;
use App\Domain\Command\Page\EditPageCommand;
use App\Domain\Contracts\UserInterface;
use App\Entity\Page;
use App\Factory\OrderFilterFactory;
use App\Factory\PaginationFactory;
use App\Factory\SearchFilterFactory;
use App\Form\PageType;
use App\Repository\CookieConsentRepository;
use App\Repository\HospitalRepository;
use App\Repository\PageRepository;
use App\Service\Filters\OrderFilter;
use App\Service\Filters\PageFilter;
use App\Service\Filters\SearchFilter;
use App\Service\FilterService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_ADMIN')]
#[Route('/settings/page')]
class PageController extends AbstractController
{
    private FilterService $filterService;

    private MessageBusInterface $messageBus;

    public function __construct(FilterService $filterService, MessageBusInterface $messageBus)
    {
        $this->filterService = $filterService;
        $this->messageBus = $messageBus;
    }

    #[Route('/', name: 'app_settings_page_index')]
    public function index(Request $request, PageRepository $pageRepository, OrderFilterFactory $orderFilterFactory, SearchFilterFactory $searchFilterFactory): Response
    {
        $this->filterService->setRequest($request);
        $this->filterService->configureFilters([PageFilter::Param, OrderFilter::Param, SearchFilter::Param]);

        $paginator = $pageRepository->getPaginator($this->filterService);

        $orderFilterFactory->setHiddenFields([
            SearchFilter::Param => $this->filterService->getValue(SearchFilter::Param),
        ]);

        $sortForm = $orderFilterFactory->setSortable(HospitalRepository::SORTABLE)->setAction($this->generateUrl('app_settings_page_index'))->getForm();

        $searchFilterFactory->setHiddenFields([
            OrderFilter::Param => $this->filterService->getValue(OrderFilter::Param),
        ]);

        $searchForm = $searchFilterFactory->setAction($this->generateUrl('app_settings_page_index'))->getForm();
        $searchForm->handleRequest($request);

        return $this->renderForm('settings/page/index.html.twig', [
            'filters' => $this->filterService->getFilterDto(),
            'sortForm' => $sortForm,
            'searchForm' => $searchForm,
            'entities' => $paginator,
            'pages' => PaginationFactory::create($this->filterService->getValue(PageFilter::Param), count($paginator), CookieConsentRepository::PER_PAGE),
        ]);
    }

    #[Route('/new', name: 'app_settings_page_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PageRepository $pageRepository): Response
    {
        $page = new Page();

        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        /** @var UserInterface $user */
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            $command = new CreatePageCommand(
                $user,
                $page->getTitle(),
                $page->getType(),
                $page->getStatus(),
                $page->getContent()
            );

            try {
                $this->messageBus->dispatch($command);
            } catch (HandlerFailedException) {
                $this->addFlash('danger', 'Sorry, something went wrong. Please try again later!');
            }

            /** @var ?Page $page */
            $page = $pageRepository->findOneBy(['title' => $command->getTitle()]);

            if (null === $page) {
                throw new \RuntimeException('Sorry, something went wrong. Please try again later.');
            }

            return $this->redirectToRoute('app_settings_page_show', ['id' => $page->getId()]);
        }

        return $this->renderForm('settings/page/new.html.twig', [
            'page' => $page,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_settings_page_show', methods: ['GET'])]
    public function show(Page $page): Response
    {
        return $this->render('settings/page/show.html.twig', [
            'page' => $page,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_settings_page_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Page $page): Response
    {
        $form = $this->createForm(PageType::class, $page);
        $form->handleRequest($request);

        /** @var UserInterface $user */
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            $command = new EditPageCommand(
                $page->getId(),
                $user,
                $page->getTitle(),
                $page->getType(),
                $page->getStatus(),
                $page->getContent()
            );

            try {
                $this->messageBus->dispatch($command);
            } catch (HandlerFailedException) {
                $this->addFlash('danger', 'Sorry, something went wrong. Please try again later!');

                return $this->renderForm('settings/page/edit.html.twig', [
                    'page' => $page,
                    'form' => $form,
                ]);
            }

            $this->addFlash('success', 'Page '.$page->getTitle().' has been edited.');

            return $this->redirectToRoute('app_settings_page_show', ['id' => $page->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('settings/page/edit.html.twig', [
            'page' => $page,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_settings_page_delete', methods: ['POST'])]
    public function delete(Request $request, Page $page): Response
    {
        $CsrfToken = $request->request->get('_token');

        if ($this->isCsrfTokenValid('delete'.$page->getId(), $CsrfToken)) {
            $command = new DeletePageCommand($page->getId());

            try {
                $this->messageBus->dispatch($command);
            } catch (HandlerFailedException) {
                $this->addFlash('danger', 'Sorry, something went wrong. Please try again later!');
            }

            $this->addFlash('success', 'Page '.$page->getTitle().' has been deleted.');
        }

        return $this->redirectToRoute('app_settings_page_index', [], Response::HTTP_SEE_OTHER);
    }
}
