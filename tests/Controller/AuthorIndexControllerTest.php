<?php
declare(strict_types = 1);

namespace Tests\App\Controller;

use App\BlogsService\Domain\Author;
use App\BlogsService\Domain\ValueObject\FileID;
use App\BlogsService\Infrastructure\IsiteResult;
use App\BlogsService\Service\AuthorService;
use App\BlogsService\Service\PostService;
use Symfony\Component\DomCrawler\Crawler;
use Tests\App\BaseWebTestCase;
use Tests\App\Builders\AuthorBuilder;
use Tests\App\Builders\PostBuilder;

/**
 * @covers \App\Controller\AuthorIndexController
 */
class AuthorIndexControllerTest extends BaseWebTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->setTestBlog();
        $this->setTestTags();
    }

    public function testBlogWithAuthorsDisplaysAuthorsAndPosts()
    {
        $crawler = $this->getCrawlerForPage(
            [
                AuthorBuilder::default()->withName('Alice Smith')->withFileId(new FileID('alicesmith'))->build(),
                AuthorBuilder::default()->withName('Bob Jones')->withFileId(new FileID('bobjones'))->build(),
            ],
            [
                'alicesmith' => new iSiteResult(1, 1, 4, [PostBuilder::default()->withTitle('Alice\'s Post')->build()]),
                'bobjones' => new iSiteResult(1, 1, 4, [PostBuilder::default()->withTitle('Bob\'s Post')->build()]),
            ]
        );

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
        $crawler = $this->getCrawlerForPage(
            [
                AuthorBuilder::default()->withName('Alice Smith')->withFileId(new FileID('alicesmith'))->build(),
            ],
            [
                'alicesmith' => new iSiteResult(1, 1, 0, []),
            ]
        );

        $authorList = $crawler->filterXPath('//ol//li');

        $firstAuthor = $authorList->first();
        $firstAuthorAuthor = $firstAuthor->filterXPath('//div[@class="grid 1/3@bpw"]');
        $firstAuthorPost = $firstAuthor->filterXPath('//div[@class="grid 2/3@bpw"]');

        $this->assertEquals('Alice Smith', trim($firstAuthorAuthor->filterXPath('//h2')->text()));
        $this->assertEquals('There are no posts by this author', trim($firstAuthorPost->text()));
    }

    public function testBlogWithNoAuthors()
    {
        $crawler = $this->getCrawlerForPage([], []);
        $this->assertEquals('There are no results', $crawler->filterXPath('//h1')->last()->text());
    }

    /**
     * @param Author[] $authors
     * @param IsiteResult[] $posts
     * @return Crawler
     */
    private function getCrawlerForPage(array $authors, array $posts): Crawler
    {
        $this->createAuthorsByBlog($authors);
        $this->createPostsForAuthors($posts);

        return $this->client->request('GET', '/blogs/testblog/authors');
    }

    /**
     * @param Author[] $authors
     */
    private function createAuthorsByBlog(array $authors)
    {
        $iSiteResult = new iSiteResult(1, 10, count($authors), $authors);

        $authorService = $this->createMock(AuthorService::class);
        $authorService
            ->expects($this->once())
            ->method('getAuthorsByBlog')
            ->willReturn($iSiteResult);

        $this->client->getContainer()->set(AuthorService::class, $authorService);
    }

    /**
     * @param IsiteResult[] $posts
     */
    private function createPostsForAuthors(array $posts)
    {
        $postService = $this->createMock(PostService::class);
        $postService
            ->expects($this->once())
            ->method('getPostsForAuthors')
            ->willReturn($posts);

        $this->client->getContainer()->set(PostService::class, $postService);
    }
}
