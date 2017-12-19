<?php
declare(strict_types = 1);

namespace Tests\App\Controller;

use App\BlogsService\Infrastructure\IsiteResult;
use App\BlogsService\Service\AuthorService;
use App\BlogsService\Service\BlogService;
use App\BlogsService\Service\PostService;
use App\BlogsService\Service\TagService;
use Tests\App\BaseWebTestCase;
use Tests\App\Builders\AuthorBuilder;
use Tests\App\Builders\BlogBuilder;
use Tests\App\Builders\PostBuilder;
use Tests\App\Builders\TagBuilder;

/**
 * @covers \App\Controller\AuthorShowController
 */
class AuthorShowControllerTest extends BaseWebTestCase
{
    public function testAuthorWithPosts()
    {
        $client = static::createClient();

        $blog = BlogBuilder::default()->build();

        $blogService = $this->createMock(BlogService::class);
        $blogService->method('getBlogById')->willReturn($blog);

        $client->getContainer()->set(BlogService::class, $blogService);

        $author = AuthorBuilder::default()
            ->withName('John Smith')
            ->build();

        $authorService = $this->createMock(AuthorService::class);
        $authorService->method('getAuthorByGuid')->willReturn($author);

        $client->getContainer()->set(AuthorService::class, $authorService);

        $posts = [
            PostBuilder::default()->build(),
            PostBuilder::default()->build(),
        ];

        $postIsiteResult = new iSiteResult(1, 10, count($posts), $posts);

        $postService = $this->createMock(PostService::class);
        $postService->method('getPostsByAuthor')->willReturn($postIsiteResult);

        $client->getContainer()->set(PostService::class, $postService);

        $tags = [
            TagBuilder::default()->build(),
            TagBuilder::default()->build(),
        ];

        $isiteResultTags = new IsiteResult(1, 1, count($tags), $tags);

        $tagService = $this->createMock(TagService::class);
        $tagService->method('getTagsByBlog')->willReturn($isiteResultTags);

        $client->getContainer()->set(TagService::class, $tagService);

        $crawler = $client->request('GET', '/blogs/testblog/authors/a85738d8-bb18-4a7c-8418-68db6a47661f');

        $authorName = $crawler->filterXPath('//h1')->first()->text();
        $this->assertEquals('John Smith', $authorName);

        $authorPostsCount = $crawler->filterXPath('//p[@class="no-margin text--shout"]//strong')->first()->text();
        $this->assertEquals('Blog posts in total 2', $authorPostsCount);

        $postsDisplayed = $crawler->filterXPath('//ol//li')->count();
        $this->assertEquals(2, $postsDisplayed);
    }

    public function testAuthorNoPosts()
    {
        $client = static::createClient();

        $blog = BlogBuilder::default()->build();

        $blogService = $this->createMock(BlogService::class);
        $blogService->method('getBlogById')->willReturn($blog);

        $client->getContainer()->set(BlogService::class, $blogService);

        $author = AuthorBuilder::default()
            ->withName('John Smith')
            ->build();

        $authorService = $this->createMock(AuthorService::class);
        $authorService->method('getAuthorByGuid')->willReturn($author);

        $client->getContainer()->set(AuthorService::class, $authorService);

        $posts = [];

        $postIsiteResult = new iSiteResult(1, 10, count($posts), $posts);

        $postService = $this->createMock(PostService::class);
        $postService->method('getPostsByAuthor')->willReturn($postIsiteResult);

        $client->getContainer()->set(PostService::class, $postService);

        $tags = [
            TagBuilder::default()->build(),
            TagBuilder::default()->build(),
        ];

        $isiteResultTags = new IsiteResult(1, 1, count($tags), $tags);

        $tagService = $this->createMock(TagService::class);
        $tagService->method('getTagsByBlog')->willReturn($isiteResultTags);

        $client->getContainer()->set(TagService::class, $tagService);

        $crawler = $client->request('GET', '/blogs/testblog/authors/a85738d8-bb18-4a7c-8418-68db6a47661f');

        $authorName = $crawler->filterXPath('//h1')->first()->text();
        $this->assertEquals('John Smith', $authorName);

        $authorPostsCount = $crawler->filterXPath('//p[@class="no-margin text--shout"]//strong');
        $this->assertEquals(0, $authorPostsCount->count());

        $postsDisplayed = $crawler->filterXPath('//ol//li')->count();
        $this->assertEquals(0, $postsDisplayed);
    }

    public function testNoAuthor()
    {
        $client = static::createClient();

        $blog = BlogBuilder::default()->build();

        $blogService = $this->createMock(BlogService::class);
        $blogService->method('getBlogById')->willReturn($blog);

        $client->getContainer()->set(BlogService::class, $blogService);

        $authorService = $this->createMock(AuthorService::class);
        $authorService->method('getAuthorByGuid')->willReturn(null);

        $client->getContainer()->set(AuthorService::class, $authorService);
        $client->getContainer()->set(PostService::class, $this->createMock(PostService::class));
        $client->getContainer()->set(TagService::class, $this->createMock(TagService::class));

        $client->request('GET', '/blogs/testblog/authors/a85738d8-bb18-4a7c-8418-68db6a47661f');
        $this->assertResponseStatusCode($client, 404);
    }
}
