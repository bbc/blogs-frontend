<?php
declare(strict_types = 1);

namespace Tests\App\Controller;

use App\BlogsService\Domain\Post;
use App\BlogsService\Domain\Tag;
use App\BlogsService\Infrastructure\IsiteResult;
use App\BlogsService\Service\PostService;
use App\BlogsService\Service\TagService;
use Symfony\Component\DomCrawler\Crawler;
use Tests\App\BaseWebTestCase;
use Tests\App\Builders\PostBuilder;
use Tests\App\Builders\TagBuilder;

/**
 * @covers \App\Controller\TagShowController
 */
class TagShowControllerTest extends BaseWebTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->setTestBlog();
    }

    public function testTagShowWithPosts()
    {
        $crawler = $this->getCrawlerForPage(
            TagBuilder::default()->withName('Test Tag')->build(),
            [
                PostBuilder::default()->build(),
                PostBuilder::default()->build(),
            ]
        );

        $title = $crawler->filterXPath('//div//h1')->first()->text();
        $this->assertEquals('Tagged with: Test Tag', $title);

        $postCountTitle = $crawler->filterXPath('//div//h2')->first()->text();
        $this->assertEquals('Posts (2)', $postCountTitle);

        $postDisplayCount = $crawler->filterXPath('//ol//li')->count();
        $this->assertEquals(2, $postDisplayCount);
    }

    public function testTagShowNoPosts()
    {
        $this->getCrawlerForPage(
            TagBuilder::default()->withName('Test Tag')->build(),
            []
        );

        $this->assertResponseStatusCode($this->client, 404);
    }

    public function testNoTag()
    {
        $this->createTagById(null);
        $this->client->getContainer()->set(PostService::class, $this->createMock(PostService::class));

        $this->client->request('GET', '/blogs/testblog/tags/testtag');
        $this->assertResponseStatusCode($this->client, 404);
    }

    /**
     * @param Tag|null $tag
     * @param Post[] $posts
     * @return Crawler
     */
    private function getCrawlerForPage(?Tag $tag, array $posts): Crawler
    {
        $this->createTagById($tag);
        $this->createPostsByTag($posts);

        return $this->client->request('GET', '/blogs/testblog/tags/testtag');
    }

    private function createTagById(?Tag $tag)
    {
        $tagService = $this->createMock(TagService::class);
        $tagService
            ->expects($this->once())
            ->method('getTagById')
            ->willReturn($tag);

        $this->client->getContainer()->set(TagService::class, $tagService);
    }

    private function createPostsByTag($posts)
    {
        $iSiteResult = new IsiteResult(1, 10, count($posts), $posts);

        $postService = $this->createMock(PostService::class);
        $postService
            ->expects($this->once())
            ->method('getPostsByTag')
            ->willReturn($iSiteResult);

        $this->client->getContainer()->set(PostService::class, $postService);
    }
}
