<?php
declare(strict_types = 1);
namespace Tests\App\BlogsService\Query\IsiteQuery;

use App\BlogsService\Query\IsiteQuery\SearchQuery;
use PHPUnit\Framework\TestCase;

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
                    'or' => [
                        [
                            'blog-name', 'contains', '*',
                        ],
                    ],
                ]
            )
            ->setSort([['elementPath' => '/*:form/*:metadata/*:blog-name']])
            ->setDepth(0)
            ->setUnfiltered(true);

        $queryResult = $searchQuery->getPath();

        $expected = (object) [
            'searchChildrenOfProject' => 'blogs',
            'fileType' => 'blogsmetadata',
            'query' => [
                'or' => [['blog-name', 'contains', '*']],
            ],
            'sort' => [['elementPath' => '/*:form/*:metadata/*:blog-name']],
            'depth' => 0,
            'unfiltered' => true,
        ];

        $this->assertAttributeEquals($expected, 'q', $searchQuery);

        $this->assertEquals('/search?q=' . urlencode(json_encode($expected)), $searchQuery->getPath());
    }
}
