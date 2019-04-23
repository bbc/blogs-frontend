<?php
declare(strict_types = 1);

namespace Tests\App\Ds\Molecule\Paginator;

use App\Ds\Molecule\Paginator\PaginatorPresenter;
use Tests\App\BaseTemplateTestCase;

class PaginatorPresenterTest extends BaseTemplateTestCase
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


    /**
     * Pagination rules

        Actual Output
        ————

        ~ == spacer-hidden

        1 item - no pagination
        2 items - all items shown
        3 items - all items shown
        4 items - all items shown
        5 items - all items shown

        6-1 = 12345~6
        6-2 = 12345~6
        6-3 = 12345~6
        6-4 = 1~23456
        6-5 = 1~23456
        6-6 = 1~23456

        7-1 = 123456~7
        7-2 = 123456~7
        7-3 = 123456~7
        7-4 = 1~23456~7
        7-5= 1~234567
        7-6=1~234567
        7-7=1~234567

        8-1 = 12345…8
        8-2 = 12345…8
        8-3 = 12345…8
        8-4 = 1~234567~8
        8-5 = 1~234567~8
        8-6 = 1…45678
        8-7 = 1…45678
        8-8 = 1…45678

        9-1 = 12345…9
        9-2 = 12345…9
        9-3 = 12345…9
        9-4 = 1~23456…9
        9-5 = 1~2345678~9
        9-6 = 1…45678~9
        9-7 = 1…456789
        9-8 = 1…56789
        9-9 = 1…56789

        Full screen
        ———————

        1 item - no pagination
        2 items - all items shown
        3 items - all items shown
        4 items - all items shown
        5 items - all items shown
        6 items - all items shown
        7 items - all items shown

        8-1 = 12345…8
        8-2 = 12345…8
        8-3 = 12345…8
        8-4 = 12345678
        8-5 = 12345678
        8-6 = 1…45678
        8-7 = 1…45678
        8-8 = 1…45678

        9-1 = 12345…9
        9-2 = 12345…9
        9-3 = 12345…9
        9-4 = 123456...9
        9-5 = 123456789
        9-6 = 1…456789
        9-7 = 1…56789
        9-8 = 1…56789

        Mobile
        ———————

        1 item - no pagination
        2 items - all items shown
        3 items - all items shown
        4 items - all items shown
        5 items - all items shown

        6-1 = 123…6
        6-2 = 123…6
        6-3 = 123…6
        6-4 = 1…456
        6-5 = 1…456
        6-6 = 1…456

        7-1 = 123…7
        7-2 = 123…7
        7-3 = 123…7
        7-4 = 1…4…7
        7-5= 1…567
        7-6=1…567
        7-7=1…567

        8-1 = 123…8
        8-2 = 123...8
        8-3 = 123…8
        8-4 = 1…4…8
        8-5 = 1…5…8
        8-6 = 1…678
        8-7 = 1…678
        8-8 = 1…678

        9-1 = 123…9
        9-2 = 123…9
        9-3 = 123…9
        9-4 = 1…4…9
        9-5 = 1…5…9
        9-6 = 1…6…9
        9-7 = 1…789
        9-8 = 1…789
        9-9 = 1…789
     */
    public function paginationDataProvider(): array
    {
        return [
            'two-pages' => [
                'current-page' => 1,
                'per-page' => 10,
                'total' => 11,
                'expected-pages' => [1, 2],
            ],
            'three-pages' => [
                'current-page' => 1,
                'per-page' => 10,
                'total' => 30,
                'expected-pages' => [1, 2, 3],
            ],
            'four-pages' => [
                'current-page' => 1,
                'per-page' => 10,
                'total' => 40,
                'expected-pages' => [1, 2, 3, 4],
            ],
            'five-pages' => [
                'current-page' => 1,
                'per-page' => 10,
                'total' => 50,
                'expected-pages' => [1, 2, 3, 4, 5],
            ],
            'six-pages-1' => [
                'current-page' => 1,
                'per-page' => 10,
                'total' => 60,
                'expected-pages' => [1, 2, 3, 4, 5, 'spacer-hidden', 6],
            ],
            'six-pages-2' => [
                'current-page' => 2,
                'per-page' => 10,
                'total' => 60,
                'expected-pages' => [1, 2, 3, 4, 5, 'spacer-mobile', 6],
            ],
            'six-pages-3' => [
                'current-page' => 3,
                'per-page' => 10,
                'total' => 60,
                'expected-pages' => [1, 2, 3, 4, 5, 'spacer-mobile', 6],
            ],
            'six-pages-4' => [
                'current-page' => 4,
                'per-page' => 10,
                'total' => 60,
                'expected-pages' => [1, 'spacer-mobile', 2, 3, 4, 5, 6],
            ],
            'six-pages-5' => [
                'current-page' => 5,
                'per-page' => 10,
                'total' => 60,
                'expected-pages' => [1, 'spacer-mobile', 2, 3, 4, 5, 6],
            ],
            'six-pages-6' => [
                'current-page' => 6,
                'per-page' => 10,
                'total' => 60,
                'expected-pages' => [1, 'spacer-hidden', 2, 3, 4, 5, 6],
            ],
            'seven-pages-1' => [
                'current-page' => 1,
                'per-page' => 10,
                'total' => 62,
                'expected-pages' => [1, 2, 3, 4, 5, 6, 'spacer-hidden', 7],
            ],
            'seven-pages-2' => [
                'current-page' => 2,
                'per-page' => 10,
                'total' => 62,
                'expected-pages' => [1, 2, 3, 4, 5, 6, 'spacer-hidden', 7],
            ],
            'seven-pages-3' => [
                'current-page' => 3,
                'per-page' => 10,
                'total' => 62,
                'expected-pages' => [1, 2, 3, 4, 5, 6, 'spacer-mobile', 7],
            ],
            'seven-pages-4' => [
                'current-page' => 4,
                'per-page' => 10,
                'total' => 62,
                'expected-pages' => [1, 'spacer-mobile', 2, 3, 4, 5, 6, 'spacer-mobile', 7],
            ],
            'seven-pages-5' => [
                'current-page' => 5,
                'per-page' => 10,
                'total' => 62,
                'expected-pages' => [1, 'spacer-mobile', 2, 3, 4, 5, 6, 7],
            ],
            'seven-pages-6' => [
                'current-page' => 6,
                'per-page' => 10,
                'total' => 62,
                'expected-pages' => [1, 'spacer-hidden', 2, 3, 4, 5, 6, 7],
            ],
            'seven-pages-7' => [
                'current-page' => 7,
                'per-page' => 10,
                'total' => 62,
                'expected-pages' => [1, 'spacer-hidden', 2, 3, 4, 5, 6, 7],
            ],
            'eight-pages-1' => [
                'current-page' => 1,
                'per-page' => 10,
                'total' => 79,
                'expected-pages' => [1, 2, 3, 4, 5, 'spacer', 8],
            ],
            'eight-pages-2' => [
                'current-page' => 2,
                'per-page' => 10,
                'total' => 79,
                'expected-pages' => [1, 2, 3, 4, 5, 'spacer', 8],
            ],
            'eight-pages-3' => [
                'current-page' => 3,
                'per-page' => 10,
                'total' => 79,
                'expected-pages' => [1, 2, 3, 4, 5, 'spacer', 8],
            ],
            'eight-pages-4' => [
                'current-page' => 4,
                'per-page' => 10,
                'total' => 79,
                'expected-pages' => [1, 'spacer-mobile', 2, 3, 4, 5, 6, 7, 'spacer-mobile', 8],
            ],
            'eight-pages-5' => [
                'current-page' => 5,
                'per-page' => 10,
                'total' => 79,
                'expected-pages' => [1, 'spacer-mobile', 2, 3, 4, 5, 6, 7, 'spacer-mobile', 8],
            ],
            'eight-pages-6' => [
                'current-page' => 6,
                'per-page' => 10,
                'total' => 79,
                'expected-pages' => [1, 'spacer', 4, 5, 6, 7, 8],
            ],
            'eight-pages-7' => [
                'current-page' => 7,
                'per-page' => 10,
                'total' => 79,
                'expected-pages' => [1, 'spacer', 4, 5, 6, 7, 8],
            ],
            'eight-pages-8' => [
                'current-page' => 8,
                'per-page' => 10,
                'total' => 79,
                'expected-pages' => [1, 'spacer', 4, 5, 6, 7, 8],
            ],
            'nine-pages-1' => [
                'current-page' => 1,
                'per-page' => 10,
                'total' => 85,
                'expected-pages' => [1, 2, 3, 4, 5, 'spacer', 9],
            ],
            'nine-pages-2' => [
                'current-page' => 2,
                'per-page' => 10,
                'total' => 85,
                'expected-pages' => [1, 2, 3, 4, 5, 'spacer', 9],
            ],
            'nine-pages-3' => [
                'current-page' => 3,
                'per-page' => 10,
                'total' => 85,
                'expected-pages' => [1, 2, 3, 4, 5, 'spacer', 9],
            ],
            'nine-pages-4' => [
                'current-page' => 4,
                'per-page' => 10,
                'total' => 85,
                'expected-pages' => [1, 'spacer-mobile', 2, 3, 4, 5, 6, 'spacer', 9],
            ],
            'nine-pages-5' => [
                'current-page' => 5,
                'per-page' => 10,
                'total' => 85,
                'expected-pages' => [1, 'spacer-mobile', 2, 3, 4, 5, 6, 7, 8, 'spacer-mobile', 9],
            ],
            'nine-pages-6' => [
                'current-page' => 6,
                'per-page' => 10,
                'total' => 85,
                'expected-pages' => [1, 'spacer', 4, 5, 6, 7, 8, 'spacer-mobile', 9],
            ],
            'nine-pages-7' => [
                'current-page' => 7,
                'per-page' => 10,
                'total' => 85,
                'expected-pages' => [1, 'spacer', 5, 6, 7, 8, 9],
            ],
            'nine-pages-8' => [
                'current-page' => 8,
                'per-page' => 10,
                'total' => 85,
                'expected-pages' => [1, 'spacer', 5, 6, 7, 8, 9],
            ],
            'nine-pages-9' => [
                'current-page' => 9,
                'per-page' => 10,
                'total' => 85,
                'expected-pages' => [1, 'spacer', 5, 6, 7, 8, 9],
            ],
            'ten-pages-1' => [
                'current-page' => 1,
                'per-page' => 10,
                'total' => 100,
                'expected-pages' => [1, 2, 3, 4, 5, 'spacer', 10],
            ],
            'ten-pages-2' => [
                'current-page' => 2,
                'per-page' => 10,
                'total' => 100,
                'expected-pages' => [1, 2, 3, 4, 5, 'spacer', 10],
            ],
            'ten-pages-3' => [
                'current-page' => 3,
                'per-page' => 10,
                'total' => 100,
                'expected-pages' => [1, 2, 3, 4, 5, 'spacer', 10],
            ],
            'ten-pages-4' => [
                'current-page' => 4,
                'per-page' => 10,
                'total' => 100,
                'expected-pages' => [1, 'spacer-mobile', 2, 3, 4, 5, 6, 'spacer', 10],
            ],
            'ten-pages-5' => [
                'current-page' => 5,
                'per-page' => 10,
                'total' => 100,
                'expected-pages' => [1, 'spacer-mobile', 2, 3, 4, 5, 6, 7, 'spacer', 10],
            ],
            'ten-pages-6' => [
                'current-page' => 6,
                'per-page' => 10,
                'total' => 100,
                'expected-pages' => [1, 'spacer', 4, 5, 6, 7, 8, 9, 'spacer-mobile', 10],
            ],
            'ten-pages-7' => [
                'current-page' => 7,
                'per-page' => 10,
                'total' => 100,
                'expected-pages' => [1, 'spacer', 5, 6, 7, 8, 9, 'spacer-mobile', 10],
            ],
            'ten-pages-8' => [
                'current-page' => 8,
                'per-page' => 10,
                'total' => 100,
                'expected-pages' => [1, 'spacer', 6, 7, 8, 9, 10],
            ],
            'ten-pages-9' => [
                'current-page' => 9,
                'per-page' => 10,
                'total' => 100,
                'expected-pages' => [1, 'spacer', 6, 7, 8, 9, 10],
            ],
            'ten-pages-10' => [
                'current-page' => 10,
                'per-page' => 10,
                'total' => 100,
                'expected-pages' => [1, 'spacer', 6, 7, 8, 9, 10],
            ],
            'eleven-pages-1' => [
                'current-page' => 1,
                'per-page' => 10,
                'total' => 110,
                'expected-pages' => [1, 2, 3, 4, 5, 'spacer', 11],
            ],
            'eleven-pages-2' => [
                'current-page' => 2,
                'per-page' => 10,
                'total' => 110,
                'expected-pages' => [1, 2, 3, 4, 5, 'spacer', 11],
            ],
            'eleven-pages-3' => [
                'current-page' => 3,
                'per-page' => 10,
                'total' => 110,
                'expected-pages' => [1, 2, 3, 4, 5, 'spacer', 11],
            ],
            'eleven-pages-4' => [
                'current-page' => 4,
                'per-page' => 10,
                'total' => 110,
                'expected-pages' => [1, 'spacer-mobile', 2, 3, 4, 5, 6, 'spacer', 11],
            ],
            'eleven-pages-5' => [
                'current-page' => 5,
                'per-page' => 10,
                'total' => 110,
                'expected-pages' => [1, 'spacer-mobile', 2, 3, 4, 5, 6, 7, 'spacer', 11],
            ],
            'eleven-pages-6' => [
                'current-page' => 6,
                'per-page' => 10,
                'total' => 110,
                'expected-pages' => [1, 'spacer', 4, 5, 6, 7, 8, 'spacer', 11],
            ],
            'eleven-pages-7' => [
                'current-page' => 7,
                'per-page' => 10,
                'total' => 110,
                'expected-pages' => [1, 'spacer', 5, 6, 7, 8, 9, 10, 'spacer-mobile', 11],
            ],
            'eleven-pages-8' => [
                'current-page' => 8,
                'per-page' => 10,
                'total' => 110,
                'expected-pages' => [1, 'spacer', 6, 7, 8, 9, 10, 'spacer-mobile', 11],
            ],
            'eleven-pages-9' => [
                'current-page' => 9,
                'per-page' => 10,
                'total' => 110,
                'expected-pages' => [1, 'spacer', 7, 8, 9, 10, 11],
            ],
            'eleven-pages-10' => [
                'current-page' => 10,
                'per-page' => 10,
                'total' => 110,
                'expected-pages' => [1, 'spacer', 7, 8, 9, 10, 11],
            ],
            'eleven-pages-11' => [
                'current-page' => 11,
                'per-page' => 10,
                'total' => 110,
                'expected-pages' => [1, 'spacer', 7, 8, 9, 10, 11],
            ],
            'one-hundred-forty-pages-1' => [
                'current-page' => 1,
                'per-page' => 10,
                'total' => 1400,
                'expected-pages' => [1, 2, 3, 4, 5, 'spacer', 140],
            ],
            'one-hundred-forty-pages-50' => [
                'current-page' => 50,
                'per-page' => 10,
                'total' => 1400,
                'expected-pages' => [1, 'spacer', 48, 49, 50, 51, 52, 'spacer', 140],
            ],
            'one-hundred-forty-pages-140' => [
                'current-page' => 140,
                'per-page' => 10,
                'total' => 1400,
                'expected-pages' => [1, 'spacer', 136, 137, 138, 139, 140],
            ],
        ];
    }
}
