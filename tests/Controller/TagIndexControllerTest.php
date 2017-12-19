<?php
declare(strict_types = 1);

namespace Tests\App\Controller;

use App\BlogsService\Infrastructure\IsiteResult;
use App\BlogsService\Service\BlogService;
use App\BlogsService\Service\TagService;
use Tests\App\BaseWebTestCase;
use Tests\App\Builders\BlogBuilder;
use Tests\App\Builders\TagBuilder;

/**
 * @covers \App\Controller\TagIndexController
 */
class TagIndexControllerTest extends BaseWebTestCase
{
    public function testBlogWithTags()
    {
        $client = static::createClient();

        $blog = BlogBuilder::default()->build();

        $blogService = $this->createMock(BlogService::class);
        $blogService->method('getBlogById')->willReturn($blog);

        $client->getContainer()->set(BlogService::class, $blogService);

        $tags = [
            TagBuilder::default()->withName('First Tag')->build(),
            TagBuilder::default()->build(),
            TagBuilder::default()->build(),
        ];

        $iSiteResult = new IsiteResult(1, 10, count($tags), $tags);

        $tagService = $this->createMock(TagService::class);
        $tagService
            ->expects($this->exactly(2)) //tags are also included in sidebar
            ->method('getTagsByBlog')
            ->willReturn($iSiteResult);

        $client->getContainer()->set(TagService::class, $tagService);

        $crawler = $client->request('GET', '/blogs/testblog/tags');

        $tagsTitle = $crawler->filterXPath('//h1')->first()->text();
        $this->assertEquals('Tags (3)', $tagsTitle);

        $tagsList = $crawler->filterXPath('//ul')->first();
        $tagsDisplayed = $tagsList->filterXPath('//li');

        $this->assertEquals(3, $tagsDisplayed->count());

        $firstTag = $tagsDisplayed->first()->filterXPath('//a');

        $this->assertEquals('First Tag', $firstTag->text());
    }

    public function testBlogNoTags()
    {
        $client = static::createClient();

        $blog = BlogBuilder::default()->build();

        $blogService = $this->createMock(BlogService::class);
        $blogService->method('getBlogById')->willReturn($blog);

        $client->getContainer()->set(BlogService::class, $blogService);

        $tags = [];

        $iSiteResult = new IsiteResult(1, 10, count($tags), $tags);

        $tagService = $this->createMock(TagService::class);
        $tagService
            ->expects($this->exactly(2)) //tags are also included in sidebar
            ->method('getTagsByBlog')
            ->willReturn($iSiteResult);

        $client->getContainer()->set(TagService::class, $tagService);

        $crawler = $client->request('GET', '/blogs/testblog/tags');

        $tagsTitle = $crawler->filterXPath('//h1')->first()->text();
        $this->assertEquals('Tags (0)', $tagsTitle);

        $noTags = $crawler->filterXPath('//p')->first()->text();
        $this->assertEquals('There are no results', $noTags);
    }
}
