<?php
declare(strict_types=1);

namespace App\BlogsService\Query\IsiteQuery;

use InvalidArgumentException;
use stdClass;

class SearchQuery
{
    /** @var stdClass */
    private $q;

    public function __construct()
    {
        $this->q = new stdClass();
    }

    /**
     * Sets the project id
     * @param string $project
     * @return SearchQuery
     */
//    public function setProject($project)
//    {
//        $this->q->project = $project;
//        return $this;
//    }


    /**
     * Updates the namespace
     * @return SearchQuery
     */
//    public function setNamespace($project, $fileType)
//    {
//        $this->q->namespaces = (object) array(
//            'ns' => 'https://production.bbc.co.uk/isite2/project/' .
//                $project .'/'. $fileType
//        );
//        return $this;
//    }

    /**
     * Set which page of results to return.  Not setting this will result in all results being fetched
     *
     * @param  int $pageNumber
     * @return SearchQuery
     * @throws InvalidArgumentException
     */
//    public function setPage($pageNumber)
//    {
//        if (!is_int($pageNumber)) {
//            throw new InvalidArgumentException('$pageNumber must be an int');
//        }
//
//        $this->q->page = (string)$pageNumber;
//
//        return $this;
//    }

    /**
     * Set the number of results in each page
     *
     * @param  int $resultsPerPage
     * @return SearchQuery
     * @throws InvalidArgumentException
     */
//    public function setPageSize($resultsPerPage)
//    {
//        if (!is_int($resultsPerPage)) {
//            throw new InvalidArgumentException('$resultsPerPage must be an int');
//        }
//
//        if ($resultsPerPage > self::MAX_PAGE_SIZE || $resultsPerPage < 0) {
//            throw new InvalidArgumentException('$resultsPerPage must be between 0 and ' . self::MAX_PAGE_SIZE);
//        }
//
//        $this->q->pageSize = (string)$resultsPerPage;
//
//        return $this;
//    }

    /**
     * Sets the parent project for search in children
     * @param string $project
     * @return SearchQuery
     */
    public function setSearchChildrenOfProject(string $project): SearchQuery
    {
        $this->q->searchChildrenOfProject = $project;

        return $this;
    }

    public function setFileType(string $fileType): SearchQuery
    {
        $this->q->fileType = $fileType;

        return $this;
    }

    /**
     * @param string[][][] $query
     * @return SearchQuery
     */
    public function setQuery(array $query): SearchQuery
    {
        $this->q->query = $query;

        return $this;
    }

    /**
     * @param string[][] $sort
     * @return SearchQuery
     */
    public function setSort(array $sort): SearchQuery
    {
        $this->q->sort = $sort;

        return $this;
    }

    public function setDepth(int $depth): SearchQuery
    {
        $this->q->depth = (string) $depth;

        return $this;
    }

    /**
     * Sets the unfiltered (when true, search from indexes only)
     * @param bool $unfiltered
     * @return SearchQuery
     */
    public function setUnfiltered(bool $unfiltered): SearchQuery
    {
        $this->q->unfiltered = $unfiltered;

        return $this;
    }

    public function getSearchQuery(): stdClass
    {
        return $this->q;
    }
}
