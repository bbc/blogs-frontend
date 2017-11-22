<?php
declare(strict_types=1);

namespace App\BlogsService\Query\IsiteQuery;

use InvalidArgumentException;
use stdClass;

class SearchQuery
{
    const MAX_PAGE_SIZE = 20;

    /** @var stdClass */
    private $q;

    public function __construct()
    {
        $this->q = new stdClass();
    }

    public function setProject(string $project): SearchQuery
    {
        $this->q->project = 'blogs-' . $project;
        return $this;
    }

    public function setNamespace($project, $fileType)
    {
        $this->q->namespaces = new stdClass();
        $this->q->namespaces->ns = 'https://production.bbc.co.uk/isite2/project/' . $project . '/' . $fileType;

        return $this;
    }

    /**
     * Set which page of results to return.  Not setting this will result in all results being fetched
     */
    public function setPage(int $pageNumber): SearchQuery
    {
        $this->q->page = (string) $pageNumber;

        return $this;
    }

    public function setPageSize(int $resultsPerPage): SearchQuery
    {
        if ($resultsPerPage > self::MAX_PAGE_SIZE || $resultsPerPage < 0) {
            throw new InvalidArgumentException('$resultsPerPage must be between 0 and ' . self::MAX_PAGE_SIZE);
        }

        $this->q->pageSize = (string) $resultsPerPage;

        return $this;
    }

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
