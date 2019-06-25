<?php
declare(strict_types = 1);

namespace App\Controller;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Service\PostService;
use App\Ds\Molecule\DatePicker\DatePicker;
use App\Helper\ApplicationTimeProvider;
use Cake\Chronos\Chronos;

class PostByDateController extends BlogsBaseController
{
    public function __invoke(Blog $blog, int $year, int $month, PostService $postService)
    {
        if (!$this->validMonth($month)) {
            throw $this->createNotFoundException('Invalid month supplied');
        }
        $page = $this->getPageNumber();

        $postResult = $postService->getPostsByMonth($blog, $year, $month, $page);
        $totalPostsMonth = $postResult->getTotal();
        $posts = $postResult->getDomainModels();

        $paginator = $this->createPaginator($postResult);

        $nowDateTime = ApplicationTimeProvider::getLocalTime();

        $monthlyTotals = $this->getCountsForAllMonthsInChosenYear($blog, $postService, $year, $month, $totalPostsMonth, $nowDateTime);

        [$oldestPost, $latestPost] = $postService->getOldestPostAndLatestPost($blog, $nowDateTime);

        $oldestPostDate = $oldestPost ? $oldestPost->getPublishedDate() : $nowDateTime;
        $latestPostDate = $latestPost ? $latestPost->getPublishedDate() : $nowDateTime;

        $datePicker = new DatePicker($year, $month, $latestPostDate, $oldestPostDate, $monthlyTotals);

        $analyticsLabels = $this->atiAnalyticsHelper()->makeLabels('list-posts', $blog);
        $pageMetadata = $this->pageMetadataHelper()->makePageMetadata(null, $blog);

        return $this->renderBlogPage(
            'post/by_date.html.twig',
            $analyticsLabels,
            $pageMetadata,
            $blog,
            [
                'blogId' => $blog->getId(),
                'posts' => $posts,
                'totalPostsMonth' => $totalPostsMonth,
                'datePicker' => $datePicker,
                'paginatorPresenter' => $paginator,
            ]
        );
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
        unset($monthsToQuery[$viewMonth-1]);

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
