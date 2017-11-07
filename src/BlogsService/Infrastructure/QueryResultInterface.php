<?php

namespace App\BlogsService\Infrastructure;

use App\BlogsService\Domain\IsiteEntity;
use SimpleXMLElement;

interface QueryResultInterface
{
    /** @return SimpleXMLElement[] */
    public function getItems(): array;

    public function getPage(): int;

    public function getPageSize(): int;

    public function getTotal(): int;

    public function hasMorePages(): bool;

    /** @return IsiteEntity[] */
    public function getDomainModels(): array;

    /** @param IsiteEntity[] $domainModels */
    public function setDomainModels(array $domainModels);

    public function addPageOfResults(QueryResultInterface $result);
}
