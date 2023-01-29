<?php

declare(strict_types=1);

namespace App\Controller\Website\Blog;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArchiveController extends AbstractController
{
    public function __construct(
        private readonly PostRepository $postRepository,
    ) {
    }

    #[Route('/blog/archive/{year}/{month?}', name: 'app_blog_archive')]
    public function index(Request $request, int $year, ?int $month = null): Response
    {
        if ($month) {
            $archiveDate = date('F Y', mktime(0, 0, 0, $month, 1, $year));
        } else {
            $archiveDate = $year;
        }

        $paginator = $this->postRepository->getArchivePaginator($this->getPage($request), $year, $month);

        return $this->render('website/blog/archive.html.twig', [
            'archive_date' => $archiveDate,
            'paginator' => $paginator,
        ]);
    }

    private function getPage(Request $request): int
    {
        $page = $request->query->get('page');

        if (is_numeric($page) && $page > 0) {
            return (int) $page;
        }

        return 1;
    }
}
