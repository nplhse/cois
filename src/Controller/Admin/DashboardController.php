<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\CookieConsent;
use App\Entity\DispatchArea;
use App\Entity\Hospital;
use App\Entity\Import;
use App\Entity\Page;
use App\Entity\SkippedRow;
use App\Entity\State;
use App\Entity\SupplyArea;
use App\Entity\User;
use App\Repository\SkippedRowRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private SkippedRowRepository $skippedRowRepository
    ) {
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Collaborative IVENA statistics');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section();
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToUrl('Return to site', 'fa fa-undo', $this->generateUrl('app_dashboard'));
        yield MenuItem::section();
        yield MenuItem::linkToCrud('Users', 'fas fa-users', User::class);
        yield MenuItem::linkToCrud('Pages', 'fas fa-sitemap', Page::class);
        yield MenuItem::linkToCrud('Cookie Consent', 'fas fa-cookie-bite', CookieConsent::class);
        yield MenuItem::section();
        yield MenuItem::linkToCrud('State', 'fas fa-map', State::class);
        yield MenuItem::linkToCrud('Dispatch Areas', 'fas fa-map-marker', DispatchArea::class);
        yield MenuItem::linkToCrud('Supply Areas', 'fas fa-map-pin', SupplyArea::class);
        yield MenuItem::linkToCrud('Hospitals', 'fas fa-hospital', Hospital::class);
        yield MenuItem::linkToCrud('Imports', 'fas fa-file-upload', Import::class);
        yield MenuItem::linkToCrud('Skipped Rows', 'fas fa-bug', SkippedRow::class)
            ->setBadge($this->skippedRowRepository->createQueryBuilder('sr')
                ->select('count(sr.id)')
                ->getQuery()
                ->getSingleScalarResult());
    }

    public function configureActions(): Actions
    {
        return parent::configureActions()
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
}
