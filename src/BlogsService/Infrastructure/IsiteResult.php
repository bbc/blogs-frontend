<?php
declare(strict_types = 1);
namespace App\BlogsService\Infrastructure;

use App\BlogsService\Domain\IsiteEntity;

class IsiteResult
{
    /** @var int */
    private $page = null;

    /** @var int */
    private $pageSize = null;

    /** @var int */
    private $total = null;

    /** @var IsiteEntity[] */
    private $domainModels = [];

    public function __construct(int $page, int $pageSize, int $total, array $domainModels)
    {
        $this->page = $page;
        $this->pageSize = $pageSize;
        $this->total = $total;
        $this->domainModels = array_filter($domainModels);
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    /** @return IsiteEntity[] */
    public function getDomainModels(): array
    {
        return $this->domainModels;
    }

    public function hasMorePages(): bool
    {
        $totalFetchedItems  = $this->page * $this->pageSize;
        return ($totalFetchedItems < $this->total);
    }

//    public function addPageOfResults(QueryResultInterface $pageResult)
//    {
//        $this->items    = array_merge($this->items, $pageResult->getItems());
//        $this->page     = $pageResult->getPage();
//        $this->pageSize = $pageResult->getPageSize();
//    }
}
