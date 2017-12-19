<?php
declare(strict_types = 1);

namespace Tests\App\Controller;

use App\BlogsService\Domain\ValueObject\FileID;
use App\BlogsService\Infrastructure\IsiteResult;
use App\BlogsService\Service\AuthorService;
use App\BlogsService\Service\BlogService;
use App\BlogsService\Service\PostService;
use App\BlogsService\Service\TagService;
use Tests\App\BaseWebTestCase;
use Tests\App\Builders\AuthorBuilder;
use Tests\App\Builders\BlogBuilder;
use Tests\App\Builders\PostBuilder;

/**
 * @covers \App\Controller\AuthorIndexController
 */
class AuthorIndexControllerTest extends BaseWebTestCase
{
    public function testBlogWithAuthorsDisplaysAuthorsAndPosts()
    {
        $client = static::createClient();

        $blog = BlogBuilder::default()->build();

        $blogService = $this->createMock(BlogService::class);
        $blogService->method('getBlogById')->willReturn($blog);

        $client->getContainer()->set(BlogService::class, $blogService);

        $authors = [
            AuthorBuilder::default()->withName('Alice Smith')->withFileId(new FileID('alicesmith'))->build(),
            AuthorBuilder::default()->withName('Bob Jones')->withFileId(new FileID('bobjones'))->build(),
        ];

        $iSiteResult = new iSiteResult(1, 10, count($authors), $authors);

        $authorService = $this->createMock(AuthorService::class);
        $authorService
            ->expects($this->once())
            ->method('getAuthorsByBlog')
            ->willReturn($iSiteResult);

        $client->getContainer()->set(AuthorService::class, $authorService);

        $posts = [
            'alicesmith' => new iSiteResult(1, 1, 4, [PostBuilder::default()->withTitle('Alice\'s Post')->build()]),
            'bobjones' => new iSiteResult(1, 1, 4, [PostBuilder::default()->withTitle('Bob\'s Post')->build()]),
        ];

        $postService = $this->createMock(PostService::class);
        $postService
            ->expects($this->once())
            ->method('getPostsForAuthors')
            ->willReturn($posts);

        $client->getContainer()->set(PostService::class, $postService);

        $tagService = $this->createMock(TagService::class);
        $client->getContainer()->set(TagService::class, $tagService);

        $crawler = $client->request('GET', '/blogs/testblog/authors');

        $authorList = $crawler->filterXPath('//ol//li');
        $this->assertEquals(2, $authorList->count());

        $firstAuthor = $authorList->first();
        $firstAuthorAuthor = $firstAuthor->filterXPath('//div[@class="grid 1/3@bpw"]');
        $firstAuthorPost = $firstAuthor->filterXPath('//div[@class="grid 2/3@bpw"]');

        $secondAuthor = $authorList->last();
        $secondAuthorAuthor = $secondAuthor->filterXPath('//div[@class="grid 1/3@bpw"]');
        $secondAuthorPost = $secondAuthor->filterXPath('//div[@class="grid 2/3@bpw"]');

        $this->assertEquals('Alice Smith', trim($firstAuthorAuthor->filterXPath('//h2')->text()));
        $this->assertEquals('Alice\'s Post', trim($firstAuthorPost->filterXPath('//h3')->text()));

        $this->assertEquals('Bob Jones', trim($secondAuthorAuthor->filterXPath('//h2')->text()));
        $this->assertEquals('Bob\'s Post', trim($secondAuthorPost->filterXPath('//h3')->text()));
    }

    public function testAuthorWithNoPosts()
    {
        $client = static::createClient();

        $blog = BlogBuilder::default()->build();

        $blogService = $this->createMock(BlogService::class);
        $blogService->method('getBlogById')->willReturn($blog);

        $client->getContainer()->set(BlogService::class, $blogService);

        $authors = [
            AuthorBuilder::default()->withName('Alice Smith')->withFileId(new FileID('alicesmith'))->build(),
        ];

        $iSiteResult = new iSiteResult(1, 10, count($authors), $authors);

        $authorService = $this->createMock(AuthorService::class);
        $authorService
            ->expects($this->once())
            ->method('getAuthorsByBlog')
            ->willReturn($iSiteResult);

        $client->getContainer()->set(AuthorService::class, $authorService);

        $posts = [
            'alicesmith' => new iSiteResult(1, 1, 0, []),
        ];

        $postService = $this->createMock(PostService::class);
        $postService
            ->expects($this->once())
            ->method('getPostsForAuthors')
            ->willReturn($posts);

        $client->getContainer()->set(PostService::class, $postService);

        $tagService = $this->createMock(TagService::class);
        $client->getContainer()->set(TagService::class, $tagService);

        $crawler = $client->request('GET', '/blogs/testblog/authors');

        $authorList = $crawler->filterXPath('//ol//li');

        $firstAuthor = $authorList->first();
        $firstAuthorAuthor = $firstAuthor->filterXPath('//div[@class="grid 1/3@bpw"]');
        $firstAuthorPost = $firstAuthor->filterXPath('//div[@class="grid 2/3@bpw"]');

        $this->assertEquals('Alice Smith', trim($firstAuthorAuthor->filterXPath('//h2')->text()));
        $this->assertEquals('There are no posts by this author', trim($firstAuthorPost->text()));
    }

    public function testBlogWithNoAuthors()
    {
        $client = static::createClient();

        $blog = BlogBuilder::default()->build();

        $blogService = $this->createMock(BlogService::class);
        $blogService->method('getBlogById')->willReturn($blog);

        $client->getContainer()->set(BlogService::class, $blogService);

        $iSiteResult = new iSiteResult(1, 10, 0, []);

        $authorService = $this->createMock(AuthorService::class);
        $authorService
            ->expects($this->once())
            ->method('getAuthorsByBlog')
            ->willReturn($iSiteResult);

        $client->getContainer()->set(AuthorService::class, $authorService);

        $postService = $this->createMock(PostService::class);
        $postService
            ->expects($this->once())
            ->method('getPostsForAuthors')
            ->willReturn([]);

        $client->getContainer()->set(PostService::class, $postService);

        $tagService = $this->createMock(TagService::class);
        $client->getContainer()->set(TagService::class, $tagService);

        $crawler = $client->request('GET', '/blogs/testblog/authors');

        $this->assertEquals('There are no results', $crawler->filterXPath('//h1')->last()->text());
    }
}
