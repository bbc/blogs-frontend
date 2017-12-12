<?php
declare(strict_types = 1);

namespace App\Controller;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\Post;
use App\BlogsService\Service\PostService;
use App\Ds\Molecule\DatePicker\DatePicker;
use App\Ds\Molecule\Paginator\PaginatorPresenter;
use Cake\Chronos\Chronos;
use Symfony\Component\HttpFoundation\Request;

class PostByDateController extends BlogsBaseController
{
    public function __invoke(Request $request, Blog $blog, int $year, int $month, PostService $postService)
    {
        $this->setBlog($blog);

        if (!$this->validMonth($month)) {
            throw $this->createNotFoundException('Invalid month supplied');
        }

        $page = $this->getPageNumber($request);

        $postResult = $postService->getPostsByMonth($blog, $year, $month, $page);
        $totalPostsMonth = $postResult->getTotal();
        $posts = $postResult->getDomainModels();

        $paginator = null;
        if ($totalPostsMonth > $postResult->getPageSize()) {
            $paginator = new PaginatorPresenter($postResult->getPage(), $postResult->getPageSize(), $postResult->getTotal());
        }

        $nowDateTime = Chronos::now();

        /** @var Post[] $latestPosts */
        $latestPosts = $postService->getPostsByBlog($blog, $nowDateTime, 1, 1, 'desc')->getDomainModels();
        /** @var Post[] $oldestPosts */
        $oldestPosts = $postService->getPostsByBlog($blog, $nowDateTime, 1, 1, 'asc')->getDomainModels();

        $monthlyTotals = $this->getCountsForAllMonthsInChosenYear($blog, $postService, $year, $month, $totalPostsMonth, $nowDateTime);

        $latestPostDate = isset($latestPosts[0]) ? $latestPosts[0]->getPublishedDate() : $nowDateTime;
        $oldestPostDate = isset($oldestPosts[0]) ? $oldestPosts[0]->getPublishedDate() : $nowDateTime;

        $datePicker = new DatePicker($year, $month, $latestPostDate, $oldestPostDate, $monthlyTotals);

        return $this->renderWithChrome(
            'post/by_date.html.twig',
            [
                'blogId' => $blog->getId(),
                'posts' => $posts,
                'totalPostsMonth' => $totalPostsMonth,
                'datePicker' => $datePicker,
                'paginatorPresenter' => $paginator,
            ]
        );
    }

    protected function getIstatsPageType(): string
    {
        return 'post_date';
    }

    private function validMonth(int $month): bool
    {
        return ($month > 0 && $month <= 12);
    }

    /**
     * Returns a 1-indexed 12 element array of post counts, one for each month, in order
     * @return int[]
     */
    private function getCountsForAllMonthsInChosenYear(Blog $blog, PostService $postService, int $viewYear, int $viewMonth, int $viewMonthTotalPosts, Chronos $now): array
    {
        $currentYear = (int) $now->format('Y');
        $currentMonth = (int) $now->format('m');

        if ($viewYear > $currentYear) {
            return array_fill(1, 12, 0);
        }

        $monthsToQuery = [];

        // If year is in the past we need the counts for all months
        // Query all months except the one we're viewing as that's already retrieved
        if ($viewYear < $currentYear) {
            $monthsToQuery = range(1, 12);
        } else {
            // We are viewing current year, we don't want to query months that are in the future
            $monthsToQuery = range(1, $currentMonth);
        }

        // Avoid querying as we already have this count
        unset($monthsToQuery[$viewMonth]);

        $results = $postService->getPostCountForMonthsInYear($blog, $viewYear, $monthsToQuery);

        // We have already got this count
        $results[$viewMonth] = $viewMonthTotalPosts;

        // All unfetched months this year are yet to happen, so they have 0 posts
        for ($i = 1; $i < 13; $i++) {
            if (!isset($results[$i])) {
                $results[$i] = 0;
            }
        }

        return $results;
    }
}
