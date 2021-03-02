<?php

namespace App\Controller\Admin;

use App\Entity\Allocation;
use App\Entity\Hospital;
use App\Entity\Import;
use App\Entity\User;
use App\Repository\AllocationRepository;
use App\Repository\HospitalRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    private $allocationRepository;

    private $hospitalRepository;

    public function __construct(AllocationRepository $allocationRepository, HospitalRepository $hospitalRepository) {
        $this->allocationRepository = $allocationRepository;
        $this->hospitalRepository = $hospitalRepository;
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'allocations' => $this->allocationRepository->countAllocations(),
            'hospitals' => $this->hospitalRepository->countHospitals(),
        ]);
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
        yield MenuItem::linkToCrud('Import', 'fas fa-upload', Import::class);
        yield MenuItem::section('Data');
        yield MenuItem::linkToCrud('Allocations', 'fas fa-list', Allocation::class);
    }
}
