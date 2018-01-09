<?php
declare(strict_types = 1);

namespace Tests\App\Controller;

use App\BlogsService\Domain\Author;
use App\BlogsService\Domain\Post;
use App\BlogsService\Infrastructure\IsiteResult;
use App\BlogsService\Service\AuthorService;
use App\BlogsService\Service\PostService;
use Symfony\Component\DomCrawler\Crawler;
use Tests\App\BaseWebTestCase;
use Tests\App\Builders\AuthorBuilder;
use Tests\App\Builders\PostBuilder;

/**
 * @covers \App\Controller\AuthorShowController
 */
class AuthorShowControllerTest extends BaseWebTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->setTestBlog();
        $this->setTestTags();
    }

    public function testAuthorWithPosts()
    {
        $crawler = $this->getCrawlerForPage(
            AuthorBuilder::default()
                ->withName('John Smith')
                ->build(),
            [
                PostBuilder::default()->build(),
                PostBuilder::default()->build(),
            ]
        );

        $this->assertAuthorName($crawler, 'John Smith');
        $this->assertPostsDisplayedCount($crawler, 2);

        $authorPostsCount = $crawler->filterXPath('//p[@class="no-margin text--shout"]//strong')->first()->text();
        $this->assertEquals('Blog posts in total 2', $authorPostsCount);
    }

    public function testAuthorNoPosts()
    {
        $crawler = $this->getCrawlerForPage(
            AuthorBuilder::default()
                ->withName('John Smith')
                ->build(),
            []
        );

        $this->assertAuthorName($crawler, 'John Smith');
        $this->assertPostsDisplayedCount($crawler, 0);

        $authorPostsCount = $crawler->filterXPath('//p[@class="no-margin text--shout"]//strong');
        $this->assertEquals(0, $authorPostsCount->count());
    }

    public function testNoAuthor()
    {
        $this->getCrawlerForPage(null, []);
        $this->assertResponseStatusCode($this->client, 404);
    }

    private function assertAuthorName(Crawler $crawler, string $name)
    {
        $authorName = $crawler->filterXPath('//h1')->first()->text();
        $this->assertEquals($name, $authorName);
    }

    private function assertPostsDisplayedCount(Crawler $crawler, int $count)
    {
        $postsDisplayed = $crawler->filterXPath('//ol//li')->count();
        $this->assertEquals($count, $postsDisplayed);
    }

    private function getCrawlerForPage(?Author $author, array $posts): Crawler
    {
        $this->createAuthorByGuid($author);
        $this->createPostsByAuthor($posts);

        return $this->client->request('GET', '/blogs/testblog/authors/a85738d8-bb18-4a7c-8418-68db6a47661f');
    }

    /**
     * @param Post[] $posts
     */
    private function createPostsByAuthor(array $posts)
    {
        $postIsiteResult = new iSiteResult(1, 10, count($posts), $posts);

        $postService = $this->createMock(PostService::class);
        $postService->method('getPostsByAuthor')->willReturn($postIsiteResult);

        $this->client->getContainer()->set(PostService::class, $postService);
    }

    private function createAuthorByGuid(?Author $author)
    {
        $authorService = $this->createMock(AuthorService::class);
        $authorService->method('getAuthorByGuid')->willReturn($author);

        $this->client->getContainer()->set(AuthorService::class, $authorService);
    }
}
