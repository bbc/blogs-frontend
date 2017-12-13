<?php
declare(strict_types = 1);

namespace Tests\App\Controller;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\Image;
use App\BlogsService\Domain\Module\FreeText;
use App\BlogsService\Domain\Tag;
use App\BlogsService\Domain\ValueObject\FileID;
use App\BlogsService\Domain\ValueObject\Social;
use App\BlogsService\Infrastructure\IsiteResult;
use App\BlogsService\Service\BlogService;
use App\BlogsService\Service\PostService;
use App\BlogsService\Service\TagService;
use App\Builders\PostBuilder;
use App\Helper\ApplicationTimeProvider;
use Cake\Chronos\Chronos;
use Tests\App\BaseWebTestCase;

/**
 * @covers \App\Controller\PostByDateController
 */
class PostByDateControllerTest extends BaseWebTestCase
{
    /**
     * Test that when user requests a month in the current year, the counts are queried only for
     * past and present months. Test also the view month's posts are displayed.
     */
    public function testViewCurrentMonthYear()
    {
        $time = new ApplicationTimeProvider();
        $now = Chronos::create(2017, 9, 8, 13, 55);
        $time->setTestDateTime($now);

        $client = static::createClient();

        $posts = [
            PostBuilder::default()
                ->withPublishedDate(Chronos::create(2017, 9, 2, 9, 00))
                ->build(),
        ];

        $tags = $this->createTestTags();
        $blog = $this->createTestBlog();

        $isiteResultPosts = new IsiteResult(1, 20, count($posts), $posts);
        $isiteResultTags = new IsiteResult(1, 1, count($tags), $tags);

        $tagService = $this->createMock(TagService::class);
        $tagService->method('getTagsByBlog')->willReturn($isiteResultTags);

        $client->getContainer()->set(TagService::class, $tagService);

        $postService = $this->createMock(PostService::class);
        $postService->method('getPostsByMonth')->willReturn($isiteResultPosts);

        $postService
            ->method('getPostsByBlog')
            ->will($this->onConsecutiveCalls(
                new IsiteResult(1, 1, 1, [$posts[0]]),
                new IsiteResult(1, 1, 1, [
                    PostBuilder::default()
                        ->withPublishedDate(Chronos::create(2013, 4, 29, 9, 45))
                        ->build(),
                ])
            ));

        // Because the keys need to match we need to replicate how controller does this
        $expectedMonthsToQuery = [1, 2, 3, 4, 5, 6, 7, 8, 9];
        unset($expectedMonthsToQuery[8]);

        $postService
            ->expects($this->once())
            ->method('getPostCountForMonthsInYear')
            ->with($blog, 2017, $expectedMonthsToQuery)
            ->willReturn(
                [
                    1 => 3,
                    2 => 4,
                    3 => 2,
                    4 => 2,
                    5 => 4,
                    6 => 8,
                    7 => 6,
                    8 => 22,
                ]
            );

        $client->getContainer()->set(PostService::class, $postService);

        $blogService = $this->createMock(BlogService::class);
        $blogService->method('getBlogById')->willReturn($blog);

        $client->getContainer()->set(BlogService::class, $blogService);

        $crawler = $client->request('GET', '/blogs/aboutthebbc/entries/2017/09');

        // DatePicker
        $datePicker = $crawler->filterXPath('//div[@class="bbc-datepicker"]');
        $this->assertEquals(1, $datePicker->count());

        $datePickerYears = $datePicker->filterXPath('//li[contains(@class, "bbc-datepicker__box-year-number")]');
        $this->assertEquals(5, $datePickerYears->count());

        $datePickerMonths = $datePicker->filterXPath('//li[contains(@class, "bbc-datepicker__box-month-name")]');
        $this->assertEquals(12, $datePickerMonths->count());

        // check the count from the view month query is used
        $september = $datePickerMonths->last()->previousAll()->previousAll()->previousAll();
        $this->assertEquals('September (1)', trim($september->text()));

        $blogPosts = $crawler->filterXPath('//div[@itemprop="blogPost"]');
        $this->assertEquals(1, $blogPosts->count());

        $firstPostDate = $blogPosts->first()->filterXPath('//time')->text();
        $this->assertEquals('Saturday 02 September 2017, 09:00', trim($firstPostDate));

        $time->clearTestDateTime();
    }

    /**
     * Test that if a user hits a URL viewing posts in the past, the counts are queried
     * and the controller displays them. Test also the view month's posts are displayed.
     */
    public function testViewMonthYearInPast()
    {
        $time = new ApplicationTimeProvider();
        $time->setTestDateTime(Chronos::create(2017, 12, 5, 12, 51));

        $client = static::createClient();

        $posts = [
            PostBuilder::default()
                ->withPublishedDate(Chronos::create(2016, 4, 4, 9, 00))
                ->build(),
            PostBuilder::default()
                ->withPublishedDate(Chronos::create(2016, 4, 25, 19, 00))
                ->build(),
            PostBuilder::default()
                ->withPublishedDate(Chronos::create(2016, 4, 29, 9, 45))
                ->build(),
        ];

        $tags = $this->createTestTags();
        $blog = $this->createTestBlog();

        $isiteResultPosts = new IsiteResult(1, 30, count($posts), $posts);
        $isiteResultTags = new IsiteResult(1, 1, count($tags), $tags);

        $tagService = $this->createMock(TagService::class);
        $tagService->method('getTagsByBlog')->willReturn($isiteResultTags);

        $client->getContainer()->set(TagService::class, $tagService);

        $postService = $this->createMock(PostService::class);
        $postService
            ->method('getPostsByMonth')
            ->willReturn($isiteResultPosts);

        $postService
            ->method('getPostsByBlog')
            ->will($this->onConsecutiveCalls(
                new IsiteResult(1, 1, 1, [
                    PostBuilder::default()
                        ->withPublishedDate(Chronos::create(2017, 4, 29, 9, 45))
                        ->build(),
                    ]),
                new IsiteResult(1, 1, 1, [
                    PostBuilder::default()
                    ->withPublishedDate(Chronos::create(2013, 4, 29, 9, 45))
                    ->build(),
                ])
            ));

        // Because the keys need to match we need to replicate how controller does this
        $expectedMonthsToQuery = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
        unset($expectedMonthsToQuery[3]);

        $postService
            ->expects($this->once())
            ->method('getPostCountForMonthsInYear')
            ->with($blog, 2016, $expectedMonthsToQuery)
            ->willReturn([
                1 => 2,
                2 => 2,
                3 => 0,
                5 => 3,
                6 => 0,
                7 => 1,
                8 => 3,
                9 => 2,
                10 => 2,
                11 => 1,
                12 => 4,
            ]);

        $client->getContainer()->set(PostService::class, $postService);

        $blogService = $this->createMock(BlogService::class);
        $blogService
            ->method('getBlogById')
            ->willReturn($blog);

        $client->getContainer()->set(BlogService::class, $blogService);

        $crawler = $client->request('GET', '/blogs/aboutthebbc/entries/2016/04');

        $datePickerMonths = $crawler->filterXPath('//li[contains(@class, "bbc-datepicker__box-month-name")]');

        $january = $datePickerMonths->first();
        $this->assertEquals('January (2)', trim($january->text()));

        $december = $datePickerMonths->last();
        $this->assertEquals('December (4)', trim($december->text()));

        $blogPosts = $crawler->filterXPath('//div[@itemprop="blogPost"]');
        $this->assertEquals(3, $blogPosts->count());

        $firstPostDate = $blogPosts->first()->filterXPath('//time')->text();
        $this->assertEquals('Monday 04 April 2016, 09:00', trim($firstPostDate));

        $time->clearTestDateTime();
    }

    /**
     * Test that if a user hits a URL viewing posts in the future, no expensive count requests are made
     * and the controller returns counts of 0. Test also that no posts are displayed.
     */
    public function testViewMonthYearInFuture()
    {
        $time = new ApplicationTimeProvider();
        $time->setTestDateTime(Chronos::create(2016, 2, 3, 11, 30));

        $client = static::createClient();

        $posts = [];

        $isiteResult = new IsiteResult(1, 30, count($posts), $posts);

        $tagService = $this->createMock(TagService::class);
        $tagService->method('getTagsByBlog')->willReturn($isiteResult);

        $client->getContainer()->set(TagService::class, $tagService);

        $postService = $this->createMock(PostService::class);
        $postService
            ->method('getPostsByMonth')
            ->willReturn($isiteResult);

        $postService
            ->expects($this->never())
            ->method('getPostCountForMonthsInYear');

        $client->getContainer()->set(PostService::class, $postService);

        $blogService = $this->createMock(BlogService::class);
        $blogService
            ->method('getBlogById')
            ->willReturn($this->createMock(Blog::class));

        $client->getContainer()->set(BlogService::class, $blogService);

        $crawler = $client->request('GET', '/blogs/aboutthebbc/entries/2017/05');

        $datePickerMonths = $crawler->filterXPath('//li[contains(@class, "bbc-datepicker__box-month-name")]');

        $january = $datePickerMonths->first();
        $this->assertEquals('January (0)', trim($january->text()));

        $blogPosts = $crawler->filterXPath('//div[@itemprop="blogPost"]');
        $this->assertEquals(0, $blogPosts->count());

        $time->clearTestDateTime();
    }

    private function createTestBlog(): Blog
    {
        return new Blog(
            'testblog',
            'Test Blog',
            'This is the short synopsis of the test blog. It\'s short.',
            'This is the description of the test blog. It\'s not as short as the short synopsis',
            false,
            'en-GB',
            'test.testblog',
            '',
            'br-08799',
            [new FreeText('Free Text Title', 'Here is some free text, for free!')],
            new Social('@testblogtwitter', 'testblogfacebook', 'testbloggoogle'),
            null,
            null,
            new Image('p017j1r1'),
            false
        );
    }

    private function createTestTags(): array
    {
        return [
            new Tag(new FileID('tagfileid'), 'sometag'),
            new Tag(new FileID('tagfileid2'), 'someothertag'),
        ];
    }
}
