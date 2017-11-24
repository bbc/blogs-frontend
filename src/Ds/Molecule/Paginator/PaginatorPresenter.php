<?php
declare(strict_types = 1);

namespace App\Ds\Molecule\Paginator;

use App\Ds\Presenter;

class PaginatorPresenter extends Presenter
{
    /**  @var int */
    private $currentPage;

    /**  @var int */
    private $pageSize;

    /** @var int */
    private $totalItems;

    /** @var (int|string)[] */
    private $items;

    public function __construct(int $currentPage, int $pageSize, int $totalItems, array $options = [])
    {
        parent::__construct($options);
        $this->currentPage = $currentPage;
        $this->pageSize = $pageSize;
        $this->totalItems = $totalItems;

        $this->items = $this->buildItems();
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getOffset(int $item): int
    {
        return abs($item - $this->currentPage);
    }

    public function getPageCount(): int
    {
        return (int) ceil($this->totalItems / $this->pageSize);
    }

    private function buildItems(): array
    {
        $pages = $this->getPageCount();
        if ($pages <= 7 || ($pages === 8 && ($this->currentPage === 4 || $this->currentPage === 5)) || ($pages === 9 && $this->currentPage === 5)) {
            $items = [];
            for ($page = 1; $page <= $pages; $page++) {
                $items[] = $page;
            }

            return $items;
        }

        if ($this->currentPage <= 5) {
            $items = [1, 2, 3, 4, 5];
            if ($this->currentPage >= 4) {
                $items[] = 6;
                if ($this->currentPage === 5) {
                    $items[] = 7;
                }
            }
            $items[] = 'spacer';
            $items[] = $pages;

            return $items;
        }

        if ($this->currentPage >= $pages - 2) {
            return [1, 'spacer', $pages - 4, $pages - 3, $pages - 2, $pages - 1, $pages];
        }
        if ($this->currentPage === $pages - 3) {
            return [1, 'spacer', $pages - 5, $pages - 4, $pages - 3, $pages - 2, $pages - 1, $pages];
        }

        $items = [1, 'spacer', $this->currentPage - 2, $this->currentPage - 1, $this->currentPage, $this->currentPage + 1, $this->currentPage + 2];

        if ($this->currentPage <= $pages - 5) {
            $items[] = 'spacer';
            $items[] = $pages;
            return $items;
        }

        if ($this->currentPage <= $pages - 3) {
            $items[] = $this->currentPage + 3;
            if ($this->currentPage === $pages - 4) {
                $items[] = $this->currentPage + 4;
            }
        }

        return $items;
    }
}
