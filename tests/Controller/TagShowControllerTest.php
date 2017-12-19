<?php
declare(strict_types = 1);

namespace Tests\App\Controller;

use App\BlogsService\Infrastructure\IsiteResult;
use App\BlogsService\Service\BlogService;
use App\BlogsService\Service\PostService;
use App\BlogsService\Service\TagService;
use Tests\App\BaseWebTestCase;
use Tests\App\Builders\BlogBuilder;
use Tests\App\Builders\PostBuilder;
use Tests\App\Builders\TagBuilder;

/**
 * @covers \App\Controller\TagShowController
 */
class TagShowControllerTest extends BaseWebTestCase
{
    public function testTagShowWithPosts()
    {
        $client = static::createClient();

        $blog = BlogBuilder::default()->build();

        $blogService = $this->createMock(BlogService::class);
        $blogService->method('getBlogById')->willReturn($blog);

        $client->getContainer()->set(BlogService::class, $blogService);

        $tag = TagBuilder::default()->withName('Test Tag')->build();

        $tagService = $this->createMock(TagService::class);
        $tagService
            ->expects($this->once())
            ->method('getTagById')
            ->willReturn($tag);

        $client->getContainer()->set(TagService::class, $tagService);

        $posts = [
            PostBuilder::default()->build(),
            PostBuilder::default()->build(),
        ];

        $iSiteResult = new IsiteResult(1, 10, count($posts), $posts);

        $postService = $this->createMock(PostService::class);
        $postService
            ->expects($this->once())
            ->method('getPostsByTag')
            ->willReturn($iSiteResult);

        $client->getContainer()->set(PostService::class, $postService);

        $crawler = $client->request('GET', '/blogs/testblog/tags/testtag');

        $title = $crawler->filterXPath('//div//h1')->first()->text();
        $this->assertEquals('Tagged with: Test Tag', $title);

        $postCountTitle = $crawler->filterXPath('//div//h2')->first()->text();
        $this->assertEquals('Posts (2)', $postCountTitle);

        $postDisplayCount = $crawler->filterXPath('//ol//li')->count();
        $this->assertEquals(2, $postDisplayCount);
    }

    public function testTagShowNoPosts()
    {
        $client = static::createClient();

        $blog = BlogBuilder::default()->build();

        $blogService = $this->createMock(BlogService::class);
        $blogService->method('getBlogById')->willReturn($blog);

        $client->getContainer()->set(BlogService::class, $blogService);

        $tag = TagBuilder::default()->withName('Test Tag')->build();

        $tagService = $this->createMock(TagService::class);
        $tagService
            ->expects($this->once())
            ->method('getTagById')
            ->willReturn($tag);

        $client->getContainer()->set(TagService::class, $tagService);

        $posts = [];

        $iSiteResult = new IsiteResult(1, 10, count($posts), $posts);

        $postService = $this->createMock(PostService::class);
        $postService
            ->expects($this->once())
            ->method('getPostsByTag')
            ->willReturn($iSiteResult);

        $client->getContainer()->set(PostService::class, $postService);

        $client->request('GET', '/blogs/testblog/tags/testtag');
        $this->assertResponseStatusCode($client, 404);
    }

    public function testNoTag()
    {
        $client = static::createClient();

        $blog = BlogBuilder::default()->build();

        $blogService = $this->createMock(BlogService::class);
        $blogService->method('getBlogById')->willReturn($blog);

        $client->getContainer()->set(BlogService::class, $blogService);

        $tagService = $this->createMock(TagService::class);
        $tagService
            ->expects($this->once())
            ->method('getTagById')
            ->willReturn(null);

        $client->getContainer()->set(TagService::class, $tagService);
        $client->getContainer()->set(PostService::class, $this->createMock(PostService::class));

        $client->request('GET', '/blogs/testblog/tags/testtag');
        $this->assertResponseStatusCode($client, 404);
    }
}
