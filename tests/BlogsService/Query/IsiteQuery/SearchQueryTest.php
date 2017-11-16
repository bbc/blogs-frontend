<?php
declare(strict_types = 1);
namespace Tests\App\BlogsService\Query\IsiteQuery;

use App\BlogsService\Query\IsiteQuery\SearchQuery;
use PHPUnit\Framework\TestCase;
use stdClass;

class SearchQueryTest extends TestCase
{
    public function testBlogMetadataQuery()
    {
        $searchQuery = new SearchQuery();
        $searchQuery
            ->setSearchChildrenOfProject('blogs')
            ->setFileType('blogsmetadata')
            ->setQuery(
                [
                    "or" => [
                        [
                            'blog-name', 'contains', '*',
                        ],
                    ],
                ]
            )
            ->setSort([["elementPath" => "/*:form/*:metadata/*:blog-name"]])
            ->setDepth(0)
            ->setUnfiltered(true);

        $queryResult = $searchQuery->getSearchQuery();

        $this->assertInstanceOf(stdClass::class, $queryResult);

        //check the fields in the stdClass have been set correctly
        $this->assertEquals('blogs', $queryResult->searchChildrenOfProject);
        $this->assertEquals('blogsmetadata', $queryResult->fileType);
        $this->assertArrayHasKey('or', $queryResult->query);
        $this->assertArrayHasKey(0, $queryResult->query['or']);
        $this->assertArrayHasKey(0, $queryResult->query['or'][0]);
        $this->assertArrayHasKey(1, $queryResult->query['or'][0]);
        $this->assertEquals('blog-name', $queryResult->query['or'][0][0]);
        $this->assertEquals('contains', $queryResult->query['or'][0][1]);
        $this->assertEquals('*', $queryResult->query['or'][0][2]);
        $this->assertEquals('/*:form/*:metadata/*:blog-name', $queryResult->sort[0]['elementPath']);
        $this->assertEquals('0', $queryResult->depth);
        $this->assertTrue($queryResult->unfiltered);
    }
}
