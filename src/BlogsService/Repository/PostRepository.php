<?php
declare(strict_types = 1);

namespace App\BlogsService\Repository;

use App\BlogsService\Query\IsiteQuery\GuidQuery;
use App\BlogsService\Query\IsiteQuery\SearchQuery;
use Cake\Chronos\Chronos;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;

class PostRepository extends AbstractRepository
{
    public function getPostsByAuthorFileId(string $blogId, string $authorFileId, int $page, int $perpage)
    {
        $query = new SearchQuery();
        $query->setProject($blogId);
        $query->setNamespace($blogId, 'blogs-post');
        $query->setQuery([
            'ns:author',
            'contains',
            $authorFileId,
        ]);
        $query->setDepth(1);
        $query->setPage($page);
        $query->setPageSize($perpage);
        $query->setUnfiltered(true); //Experimental
        $query->setSort([
            [
                'elementPath' => '/ns:form/ns:metadata/ns:published-date',
                'direction' => 'desc',
            ],
        ]);

        return $this->getResponse($query);
    }

    public function getPostByGuid(string $guid, string $blogId = ''): ?ResponseInterface
    {
        $query = new GuidQuery();

        $query->setContentId($guid);
        $query->setDepth(1);
        if ($blogId) {
            $query->setProject($blogId);
        }

        return $this->getResponse($query);
    }

    /**
     * @param string $blogId
     * @param array $ranges Contains params for the actual query, requires the following keys:
     *                      $ranges['afterDate']    Chronos Beginning of the date range in the query
     *                      $ranges['beforeDate']   Chronos End of the date range in the query
     *                      $ranges['sort']         string Result ordering, 'asc' or 'desc'
     * @param int $depth
     * @param int $page
     * @param int $perpage
     * @return ResponseInterface[]
     */
    public function getPostsBetween(
        string $blogId,
        array $ranges,
        int $depth,
        int $page,
        int $perpage
    ): array {
        $queries = [];

        foreach ($ranges as $key => $range) {
            if (!isset($range['afterDate']) || !isset($range['beforeDate']) || !isset($range['sort'])) {
                throw new InvalidArgumentException('getPostsBetweenParallel range elements must have afterDate, beforeDate and sort indexes set');
            }

            $query = new SearchQuery();
            $query->setProject($blogId);
            $query->setNamespace($blogId, 'blogs-post');

            $query->setQuery([
                'and' => [
                    [
                        'ns:published-date',
                        '>',
                        $range['afterDate']->addSecond()->format('Y-m-d\TH:i:s.BP'),
                        'dateTime',
                    ],
                    [
                        'ns:published-date',
                        '<=',
                        $range['beforeDate']->format('Y-m-d\TH:i:s.BP'),
                        'dateTime',
                    ],
                ],
            ]);

            $query->setSort([
                [
                    'elementPath' => '/ns:form/ns:metadata/ns:published-date',
                    'direction' => $range['sort'],
                ],
            ]);
            $query->setDepth($depth);
            $query->setPage($page);
            $query->setPageSize($perpage);
            $query->setUnfiltered(true);

            $queries[$key] = $query;
        }

        return $this->getParallelResponses($queries);
    }

    public function getPostsForAuthors(string $blogId, array $authorIds, int $page, int $perpage): array
    {
        $queries = [];
        foreach ($authorIds as $authorId) {
            $query = new SearchQuery();
            $query->setProject($blogId);
            $query->setNamespace($blogId, 'blogs-post');
            $query->setQuery([
                'ns:author',
                'contains',
                $authorId,
            ]);
            $query->setDepth(1);
            $query->setPage($page);
            $query->setPageSize($perpage);
            $query->setUnfiltered(true); //Experimental
            $query->setSort([
                [
                    'elementPath' => '/ns:form/ns:metadata/ns:published-date',
                    'direction' => 'desc',
                ],
            ]);
            $queries[$authorId] = $query;
        }

        return $this->getParallelResponses($queries);
    }

    /**
     * @param string $blogId
     * @param int $year
     * @param int[] $months
     * @param int $depth
     * @param int $page
     * @param int $perpage
     * @param string $sort
     * @return ResponseInterface[]
     */
    public function getPostsForMonthsInYear(
        string $blogId,
        int $year,
        array $months,
        int $depth,
        int $page,
        int $perpage,
        string $sort
    ): array {
        $queries = [];
        foreach ($months as $month) {
            if (!\is_int($month)) {
                throw new InvalidArgumentException('Argument months must be an array of integers');
            }

            $yearMonth = Chronos::create($year, $month, 2);
            $afterDate = $yearMonth->startOfMonth();
            $beforeDate = $yearMonth->endOfMonth();

            $query = new SearchQuery();
            $query->setProject($blogId);
            $query->setNamespace($blogId, 'blogs-post');

            $query->setQuery([
                'and' => [
                    [
                        'ns:published-date',
                        '>',
                        $afterDate->addSecond()->format('Y-m-d\TH:i:s.BP'),
                        'dateTime',
                    ],
                    [
                        'ns:published-date',
                        '<=',
                        $beforeDate->format('Y-m-d\TH:i:s.BP'),
                        'dateTime',
                    ],
                ],
            ]);

            $query->setSort([
                [
                    'elementPath' => '/ns:form/ns:metadata/ns:published-date',
                    'direction' => $sort,
                ],
            ]);
            $query->setDepth($depth);
            $query->setPage($page);
            $query->setPageSize($perpage);
            $query->setUnfiltered(true);

            $queries[$month] = $query;
        }

        return $this->getParallelResponses($queries);
    }

    public function getPostsByTagFileId(string $blogId, string $tagFileId, int $page, int $perpage): ?ResponseInterface
    {
        $query = new SearchQuery();
        $query->setProject($blogId);
        $query->setNamespace($blogId, 'blogs-post');
        $query->setQuery(['ns:tag', 'contains', $tagFileId]);
        $query->setSort([
            [
                'elementPath' => '/ns:form/ns:metadata/ns:published-date',
                'direction' => 'desc',
            ],
        ]);
        $query->setDepth(1);
        $query->setPage($page);
        $query->setPageSize($perpage);
        $query->setUnfiltered(true);

        return $this->getResponse($query);
    }
}
