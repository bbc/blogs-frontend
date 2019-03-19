<?php
declare(strict_types = 1);

namespace App\Ds\Molecule\Paginator;

use App\Ds\Presenter;

class PaginatorPresenter extends Presenter
{
    /** @var int */
    private $currentPage;

    /** @var int */
    private $pageSize;

    /** @var (int|string)[] */
    private $items;
    
    /** @var int */
    private $totalItems;

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

    public function shouldApplyNoHideClass(int $item): bool
    {
        if ($this->shouldShowAllPagesWithoutSpacers()) {
            // We ALWAYS want to show ALL items if under 5, so this applies a CSS override for that case
            return true;
        }

        return $this->currentPageWithinThreeOfEnd($item);
    }

    /**
     * The mobile class should be applied to page items if they are where
     * a spacer should be on tablets up, but the spacer is not shown as
     * the previous and next items are only two apart.
     * @param int $item The page number of the item to be checked.
     * @return bool
     */
    public function shouldApplyMobileClass(int $item): bool
    {
        $pages = $this->getPageCount();
        $currentPage = $this->currentPage;
        return ($item === 2 || $item === $pages - 1) && ($currentPage === 5 || $currentPage === $pages - 4);
    }

    /**
     * This method builds the pagination items that are going to be shown.
     * @return array
     */
    private function buildItems(): array
    {
        $pages = $this->getPageCount();

        if ($this->shouldShowAllPagesWithoutSpacers()) {
            return range(1, $pages);
        }

        if ($this->normalSpacerStartHiddenSpacerEnd()) {
            $numberOfPagesAfterSpacer = max(5, ($pages - $this->currentPage) + 3);
            return array_merge([1, 'spacer'], range($pages - ($numberOfPagesAfterSpacer - 1), $pages - 1), ['spacer-mobile', $pages]);
        }

        if ($this->hiddenSpacerStartNormalSpacerEnd()) {
            $numberOfPagesBeforeSpacer = max(5, $this->currentPage + 2);
            return array_merge([1, 'spacer-mobile'], range(2, $numberOfPagesBeforeSpacer), ['spacer', $pages]);
        }

        if ($this->hiddenSpacerBothEnds()) {
            return array_merge([1, 'spacer-mobile'], range(2, $pages -1), ['spacer-mobile', $pages]);
        }

        if ($this->spacerAtStartOnly()) {
            $numberOfPagesAfterSpacer = max(5, ($pages - $this->currentPage) + 3);
            return array_merge([1, 'spacer'], range($pages - ($numberOfPagesAfterSpacer - 1), $pages));
        }

        if ($this->spacerAtEndOnly()) {
            $numberOfPagesBeforeSpacer = max(5, $this->currentPage + 2);
            return array_merge(range(1, $numberOfPagesBeforeSpacer), ['spacer', $pages]);
        }

        if ($this->hiddenSpacerAtStart()) {
            $currentPage = $this->currentPage;
            if ($currentPage === 4 || $currentPage === 5) {
                $spacer = 'spacer-mobile';
            } else {
                $spacer = 'spacer-hidden';
            }
            return array_merge([1, $spacer], range(2, $pages));
        }

        if ($this->hiddenSpacerAtEnd()) {
            $currentPage = $this->currentPage;
            if ($currentPage === $pages - 3 || $currentPage === $pages - 4) {
                $spacer = 'spacer-mobile';
            } else {
                $spacer = 'spacer-hidden';
            }
            $numberOfPagesBeforeSpacer = max($pages === 7 ? 6 : 5, $this->currentPage + 2);
            return array_merge(range(1, $numberOfPagesBeforeSpacer), [$spacer, $pages]);
        }

        return array_merge([1, 'spacer'], range($this->currentPage - 2, $this->currentPage + 2), ['spacer', $pages]);
    }

    private function shouldShowAllPagesWithoutSpacers(): bool
    {
        return $this->getPageCount() <= 5;
    }

    private function spacerAtEndOnly(): bool
    {
        return $this->currentPage < 5 && $this->spacerAtEnd();
    }

    private function spacerAtStartOnly(): bool
    {
        return $this->currentPage > $this->getPageCount() - 4 && $this->spacerAtStart();
    }

    private function hiddenSpacerBothEnds(): bool
    {
        return $this->hiddenSpacerAtStart() && $this->hiddenSpacerAtEnd();
    }

    private function hiddenSpacerStartNormalSpacerEnd(): bool
    {
        return $this->hiddenSpacerAtStart() && $this->spacerAtEnd();
    }

    private function normalSpacerStartHiddenSpacerEnd(): bool
    {
        return $this->spacerAtStart() && $this->hiddenSpacerAtEnd();
    }

    private function spacerAtStart(): bool
    {
        return ($this->currentPage > 5) && $this->desktopSizeNeedsSpacers();
    }

    private function spacerAtEnd(): bool
    {
        return ($this->currentPage <= $this->getPageCount() - 5) && $this->desktopSizeNeedsSpacers();
    }

    private function hiddenSpacerAtEnd(): bool
    {
        return ($this->currentPage <= $this->getPageCount() - 3) && !$this->spacerAtEnd();
    }

    private function hiddenSpacerAtStart(): bool
    {
        return $this->currentPage > 3 && $this->getPageCount() > 5 && !$this->spacerAtStart();
    }

    private function desktopSizeNeedsSpacers(): bool
    {
        $pageCount = $this->getPageCount();
        $eightPageException = $pageCount == 8 && in_array($this->currentPage, [4, 5]); // Show all items in this special case
        $ninePageException = $pageCount == 9 && $this->currentPage == 5; // Show all items in this special case

        return $this->getPageCount() > 7 && !$eightPageException && !$ninePageException;
    }

    private function currentPageWithinThreeOfEnd(int $item): bool
    {
        if (($item <= 3) && $this->getCurrentPage() <= 3) {
            // We should always show the first three items if the current page is less than 3
            return true;
        }
        if (($item >= ($this->getPageCount() - 2)) && $this->getCurrentPage() >= $this->getPageCount() - 2) {
            // We should show the last three items if the final page count is less than 3 from the end
            return true;
        }
        return false;
    }
}
