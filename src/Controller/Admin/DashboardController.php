<?php

namespace App\Controller\Admin;

use App\Entity\Hospital;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Collaborative IVENA statistics');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linktoRoute('Back to frontend', 'fas fa-power-off', 'default');
        yield MenuItem::section('Users');
        yield MenuItem::linkToCrud('Users', 'fas fa-users', User::class);
        yield MenuItem::section('Hospitals');
        yield MenuItem::linkToCrud('Hospitals', 'fas fa-hospital', Hospital::class);
    }
}
