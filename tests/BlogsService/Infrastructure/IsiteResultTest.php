<?php
declare(strict_types = 1);

namespace Tests\App\BlogsService\Infrastructure;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Infrastructure\IsiteResult;
use PHPUnit\Framework\TestCase;

class IsiteResultTest extends TestCase
{
    public function testConstructor()
    {
        $page = 2;
        $size = 4;
        $total = 8;
        $mockDomainModels = [
            $this->createMock(Blog::class),
            $this->createMock(Blog::class),
        ];

        $result = new IsiteResult($page, $size, $total, $mockDomainModels);

        $this->assertEquals($page, $result->getPage());
        $this->assertEquals($size, $result->getPageSize());
        $this->assertEquals($total, $result->getTotal());
        $this->assertInternalType('array', $result->getDomainModels());
        $this->assertCount(2, $result->getDomainModels());
    }

    public function testFiltersNullDomainObjects()
    {
        $mockDomainModels = [
            null,
            $this->createMock(Blog::class),
            $this->createMock(Blog::class),
            null,
            $this->createMock(Blog::class),
            null,
        ];

        $result = new IsiteResult(1, 1, 1, $mockDomainModels);

        $this->assertInternalType('array', $result->getDomainModels());
        $this->assertCount(3, $result->getDomainModels());
    }

    /** @dataProvider paginationProvider */
    public function testHasMorePages(int $page, int $size, int $total, bool $expected)
    {
        $mockBlog = $this->createMock(Blog::class);

        $result = new IsiteResult($page, $size, $total, [$mockBlog]);
        $this->assertEquals($expected, $result->hasMorePages());
    }

    public function paginationProvider(): array
    {
        return [
            'has_more_pages' => [1, 10, 20, true],
            'no_more_pages_all' => [1, 10, 10, false],
            'no_more_pages_part' => [1, 10, 8, false],
        ];
    }
}
