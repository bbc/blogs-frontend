<?php
declare(strict_types = 1);

namespace App\BlogsService\Infrastructure;

class IsiteResult
{
    /** @var int */
    private $page;

    /** @var int */
    private $pageSize;

    /** @var int */
    private $total;

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

    public function getDomainModels(): array
    {
        return $this->domainModels;
    }

    public function hasMorePages(): bool
    {
        $totalFetchedItems  = $this->page * $this->pageSize;
        return ($totalFetchedItems < $this->total);
    }
}
