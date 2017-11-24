<?php
declare(strict_types = 1);

namespace Tests\App\Ds\Molecule\Paginator;

use App\Ds\Molecule\Paginator\PaginatorPresenter;
use PHPUnit\Framework\TestCase;

class PaginatorPresenterTest extends TestCase
{
    /**
     * @dataProvider paginationDataProvider
     * @param int $currentPage
     * @param int $perPage
     * @param int $total
     * @param (int|string)[] $expectedPages
     */
    public function testPagination(int $currentPage, int $perPage, int $total, array $expectedPages)
    {
        $presenter = new PaginatorPresenter($currentPage, $perPage, $total);
        $items = $presenter->getItems();
        $this->assertCount(count($expectedPages), $items);
        foreach ($expectedPages as $key => $value) {
            $this->assertSame($value, $items[$key]);
        }
    }

    public function paginationDataProvider(): array
    {
        return [
            'two-pages' => [
                'current-page' => 1,
                'per-page' => 10,
                'total' => 11,
                'expected-pages' => [1, 2],
            ],
            'seven-pages' => [
                'current-page' => 1,
                'per-page' => 10,
                'total' => 62,
                'expected-pages' => [1, 2, 3, 4, 5, 6, 7],
            ],
            'eight-pages-page-1' => [
                'current-page' => 1,
                'per-page' => 10,
                'total' => 79,
                'expected-pages' => [1, 2, 3, 4, 5, 'spacer', 8],
            ],
            'eight-pages-page-4' => [
                'current-page' => 4,
                'per-page' => 10,
                'total' => 79,
                'expected-pages' => [1, 2, 3, 4, 5, 6, 7, 8],
            ],
            'eight-pages-page-5' => [
                'current-page' => 5,
                'per-page' => 10,
                'total' => 79,
                'expected-pages' => [1, 2, 3, 4, 5, 6, 7, 8],
            ],
            'eight-pages-page-6' => [
                'current-page' => 6,
                'per-page' => 10,
                'total' => 79,
                'expected-pages' => [1, 'spacer', 4, 5, 6, 7, 8],
            ],
            'eight-pages-page-8' => [
                'current-page' => 8,
                'per-page' => 10,
                'total' => 79,
                'expected-pages' => [1, 'spacer', 4, 5, 6, 7, 8],
            ],
            'nine-pages-page-1' => [
                'current-page' => 1,
                'per-page' => 10,
                'total' => 85,
                'expected-pages' => [1, 2, 3, 4, 5, 'spacer', 9],
            ],
            'nine-pages-page-4' => [
                'current-page' => 4,
                'per-page' => 10,
                'total' => 85,
                'expected-pages' => [1, 2, 3, 4, 5, 6, 'spacer', 9],
            ],
            'nine-pages-page-5' => [
                'current-page' => 5,
                'per-page' => 10,
                'total' => 85,
                'expected-pages' => [1, 2, 3, 4, 5, 6, 7, 8, 9],
            ],
            'nine-pages-page-6' => [
                'current-page' => 6,
                'per-page' => 10,
                'total' => 85,
                'expected-pages' => [1, 'spacer', 4, 5, 6, 7, 8, 9],
            ],
            'nine-pages-page-7' => [
                'current-page' => 7,
                'per-page' => 10,
                'total' => 85,
                'expected-pages' => [1, 'spacer', 5, 6, 7, 8, 9],
            ],
            'nine-pages-page-9' => [
                'current-page' => 9,
                'per-page' => 10,
                'total' => 85,
                'expected-pages' => [1, 'spacer', 5, 6, 7, 8, 9],
            ],
            'ten-pages-page-1' => [
                'current-page' => 1,
                'per-page' => 10,
                'total' => 95,
                'expected-pages' => [1, 2, 3, 4, 5, 'spacer', 10],
            ],
            'ten-pages-page-4' => [
                'current-page' => 4,
                'per-page' => 10,
                'total' => 95,
                'expected-pages' => [1, 2, 3, 4, 5, 6, 'spacer', 10],
            ],
            'ten-pages-page-5' => [
                'current-page' => 5,
                'per-page' => 10,
                'total' => 95,
                'expected-pages' => [1, 2, 3, 4, 5, 6, 7, 'spacer', 10],
            ],
            'ten-pages-page-6' => [
                'current-page' => 6,
                'per-page' => 10,
                'total' => 95,
                'expected-pages' => [1, 'spacer', 4, 5, 6, 7, 8, 9, 10],
            ],
            'ten-pages-page-10' => [
                'current-page' => 10,
                'per-page' => 10,
                'total' => 95,
                'expected-pages' => [1, 'spacer', 6, 7, 8, 9, 10],
            ],
            'eleven-pages-page-1' => [
                'current-page' => 1,
                'per-page' => 10,
                'total' => 101,
                'expected-pages' => [1, 2, 3, 4, 5, 'spacer', 11],
            ],
            'eleven-pages-page-4' => [
                'current-page' => 4,
                'per-page' => 10,
                'total' => 101,
                'expected-pages' => [1, 2, 3, 4, 5, 6, 'spacer', 11],
            ],
            'eleven-pages-page-5' => [
                'current-page' => 5,
                'per-page' => 10,
                'total' => 101,
                'expected-pages' => [1, 2, 3, 4, 5, 6, 7, 'spacer', 11],
            ],
            'eleven-pages-page-6' => [
                'current-page' => 6,
                'per-page' => 10,
                'total' => 101,
                'expected-pages' => [1, 'spacer', 4, 5, 6, 7, 8, 'spacer', 11],
            ],
            'eleven-pages-page-7' => [
                'current-page' => 7,
                'per-page' => 10,
                'total' => 101,
                'expected-pages' => [1, 'spacer', 5, 6, 7, 8, 9, 10, 11],
            ],
            'eleven-pages-page-8' => [
                'current-page' => 8,
                'per-page' => 10,
                'total' => 101,
                'expected-pages' => [1, 'spacer', 6, 7, 8, 9, 10, 11],
            ],
            'eleven-pages-page-9' => [
                'current-page' => 9,
                'per-page' => 10,
                'total' => 101,
                'expected-pages' => [1, 'spacer', 7, 8, 9, 10, 11],
            ],
            'eleven-pages-page-11' => [
                'current-page' => 11,
                'per-page' => 10,
                'total' => 101,
                'expected-pages' => [1, 'spacer', 7, 8, 9, 10, 11],
            ],
        ];
    }
}
