<?php
declare(strict_types = 1);

namespace Tests\App\Controller;

use App\BlogsService\Domain\Tag;
use App\BlogsService\Domain\ValueObject\FileID;
use App\BlogsService\Infrastructure\IsiteResult;
use App\BlogsService\Service\PostService;
use App\BlogsService\Service\TagService;
use Symfony\Component\DomCrawler\Crawler;
use Tests\App\BaseWebTestCase;
use Tests\App\Builders\TagBuilder;

/**
 * @covers \App\Controller\TagIndexController
 */
class TagIndexControllerTest extends BaseWebTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->setTestBlog();
    }

    public function testBlogWithTags()
    {
        $crawler = $this->getCrawlerForPage(
            [
                TagBuilder::default()->withName('First Tag')->withFileId(new FileID('tag-t1'))->build(),
                TagBuilder::default()->withFileId(new FileID('tag-t2'))->build(),
                TagBuilder::default()->withFileId(new FileID('tag-t3'))->build(),
            ],
            ['t1' => 1, 't2' => 2, 't3' => 3]
        );

        $this->assertTagsTitle($crawler, 'Tags (3)');

        $tagsList = $crawler->filterXPath('//ul')->first();
        $tagsDisplayed = $tagsList->filterXPath('//li');

        $this->assertEquals(3, $tagsDisplayed->count());

        $firstTag = $tagsDisplayed->first()->filterXPath('//h2');
        $this->assertEquals('First Tag', $firstTag->text());

        $firstTagCount = $tagsDisplayed->first()->filterXPath('//p');
        $this->assertEquals('1 post', $firstTagCount->text());
    }

    public function testBlogNoTags()
    {
        $crawler = $this->getCrawlerForPage([], []);

        $this->assertTagsTitle($crawler, 'Tags (0)');

        $noTags = $crawler->filterXPath('//p')->first()->text();
        $this->assertEquals('There are no results', $noTags);
    }

    private function assertTagsTitle(Crawler $crawler, string $title)
    {
        $tagsTitle = $crawler->filterXPath('//h1')->first()->text();
        $this->assertEquals($title, $tagsTitle);
    }

    /**
     * @param Tag[] $tags
     * @return Crawler
     */
    private function getCrawlerForPage(array $tags, array $tagPostCounts): Crawler
    {
        $iSiteResult = new IsiteResult(1, 10, count($tags), $tags);

        $tagService = $this->createMock(TagService::class);
        $tagService
            ->expects($this->exactly(2)) //tags are also included in sidebar
            ->method('getTagsByBlog')
            ->willReturn($iSiteResult);

        $this->client->getContainer()->set(TagService::class, $tagService);

        $postService = $this->createMock(PostService::class);
        $postService->method('getPostCountsForTags')->willReturn($tagPostCounts);

        $this->client->getContainer()->set(PostService::class, $postService);

        return $this->client->request('GET', '/blogs/testblog/tags');
    }
}
