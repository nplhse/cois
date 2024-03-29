<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\AuditLog;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\ContactRequest;
use App\Entity\CookieConsent;
use App\Entity\DispatchArea;
use App\Entity\Hospital;
use App\Entity\Import;
use App\Entity\Page;
use App\Entity\Post;
use App\Entity\SkippedRow;
use App\Entity\State;
use App\Entity\SupplyArea;
use App\Entity\Tag;
use App\Entity\User;
use App\Repository\CommentRepository;
use App\Repository\ContactRequestRepository;
use App\Repository\SkippedRowRepository;
use Domain\Enum\CommentStatus;
use Domain\Enum\ContactRequestStatus;
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
        private CommentRepository $commentRepository,
        private ContactRequestRepository $contactRequestRepository,
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
        yield MenuItem::linkToUrl('Return to site', 'fa fa-undo', $this->generateUrl('app_dashboard'));
        yield MenuItem::section('General');
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Users', 'fas fa-users', User::class);
        yield MenuItem::linkToCrud('Cookie Consent', 'fas fa-cookie-bite', CookieConsent::class);
        yield MenuItem::linkToCrud('Audit log', 'fas fa-chart-simple', AuditLog::class);
        yield MenuItem::section('Website');
        yield MenuItem::linkToCrud('Pages', 'fas fa-sitemap', Page::class);
        yield MenuItem::linkToCrud('label.posts', 'fas fa-file', Post::class);
        yield MenuItem::linkToCrud('label.categories', 'fas fa-tag', Category::class);
        yield MenuItem::linkToCrud('label.tags', 'fas fa-tags', Tag::class);
        yield MenuItem::linkToCrud('label.comments', 'fas fa-comment', Comment::class)
            ->setBadge($this->commentRepository->createQueryBuilder('c')
                ->select('count(c.id)')
                ->where('c.status = :status')
                ->setParameter('status', CommentStatus::SUBMITTED)
                ->getQuery()
                ->getSingleScalarResult());
        yield MenuItem::linkToCrud('Contact Requests', 'fas fa-message', ContactRequest::class)
            ->setBadge($this->contactRequestRepository->createQueryBuilder('c')
                ->select('count(c.id)')
                ->where('c.status = :status')
                ->setParameter('status', ContactRequestStatus::OPEN)
                ->getQuery()
                ->getSingleScalarResult());
        yield MenuItem::section('Data');
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
