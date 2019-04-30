<?php
declare(strict_types = 1);

namespace Tests\App\Controller;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Infrastructure\IsiteResult;
use App\BlogsService\Service\BlogService;
use App\BlogsService\Service\PostService;
use App\Helper\ApplicationTimeProvider;
use Cake\Chronos\Chronos;
use Tests\App\BaseWebTestCase;
use Tests\App\Builders\BlogBuilder;
use Tests\App\Builders\PostBuilder;

/**
 * @covers \App\Controller\PostByDateController
 */
class PostByDateControllerTest extends BaseWebTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->setTestTags();
    }

    /**
     * Test that when user requests a month in the current year, the counts are queried only for
     * past and present months. Test also the view month's posts are displayed.
     */
    public function testViewCurrentMonthYear()
    {
        ApplicationTimeProvider::setDateTime(Chronos::create(2017, 9, 8, 13, 55));

        $posts = [
            PostBuilder::default()
                ->withPublishedDate(new Chronos("2017-09-02T09:00:00.000", "Europe/London"))
                ->build(),
        ];

        $blog = BlogBuilder::default()->build();
        $this->setBlog($blog);

        $isiteResultPosts = new IsiteResult(1, 20, count($posts), $posts);

        $postService = $this->createMock(PostService::class);
        $postService->method('getPostsByMonth')->willReturn($isiteResultPosts);

        $postService
            ->method('getOldestPostAndLatestPost')
            ->willReturn(
                [
                    PostBuilder::default()
                        ->withPublishedDate(new Chronos("2013-04-29T09:45:00.000", "Europe/London"))
                        ->build(),
                    $posts[0],
                ]
            );

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

        $this->client->getContainer()->set(PostService::class, $postService);

        $crawler = $this->client->request('GET', '/blogs/aboutthebbc/entries/2017/09');

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

        $blogPosts = $crawler->filterXPath('//li[@class="br-keyline"]');
        $this->assertEquals(1, $blogPosts->count());

        $firstPostDate = $blogPosts->first()->filterXPath('//time')->text();
        $this->assertEquals('Saturday 02 September 2017, 09:00', trim($firstPostDate));
    }

    /**
     * Test that if a user hits a URL viewing posts in the past, the counts are queried
     * and the controller displays them. Test also the view month's posts are displayed.
     */
    public function testViewMonthYearInPast()
    {
        ApplicationTimeProvider::setDateTime(Chronos::create(2017, 12, 5, 12, 51));

        $posts = [
            PostBuilder::default()
                ->withPublishedDate(new Chronos("2016-04-04T09:00:00.000", "Europe/London"))
                ->build(),
            PostBuilder::default()
                ->withPublishedDate(new Chronos("2016-04-25T19:00:00.000", "Europe/London"))
                ->build(),
            PostBuilder::default()
                ->withPublishedDate(new Chronos("2016-04-29T09:45:00.000", "Europe/London"))
                ->build(),
        ];

        $blog = BlogBuilder::default()->build();
        $this->setBlog($blog);

        $isiteResultPosts = new IsiteResult(1, 30, count($posts), $posts);

        $postService = $this->createMock(PostService::class);
        $postService
            ->method('getPostsByMonth')
            ->willReturn($isiteResultPosts);

        $postService
            ->method('getOldestPostAndLatestPost')
            ->willReturn(
                [
                    PostBuilder::default()
                        ->withPublishedDate(new Chronos("2013-04-29T09:45:00.000", "Europe/London"))
                        ->build(),
                    PostBuilder::default()
                        ->withPublishedDate(new Chronos("2017-04-29T09:45:00.000", "Europe/London"))
                        ->build(),
                ]
            );

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

        $this->client->getContainer()->set(PostService::class, $postService);

        $crawler = $this->client->request('GET', '/blogs/aboutthebbc/entries/2016/04');

        $datePickerMonths = $crawler->filterXPath('//li[contains(@class, "bbc-datepicker__box-month-name")]');

        $january = $datePickerMonths->first();
        $this->assertEquals('January (2)', trim($january->text()));

        $december = $datePickerMonths->last();
        $this->assertEquals('December (4)', trim($december->text()));

        $blogPosts = $crawler->filterXPath('//li[@class="br-keyline"]');
        $this->assertEquals(3, $blogPosts->count());

        $firstPostDate = $blogPosts->first()->filterXPath('//time')->text();
        $this->assertEquals('Monday 04 April 2016, 09:00', trim($firstPostDate));
    }

    /**
     * Test that if a user hits a URL viewing posts in the future, no expensive count requests are made
     * and the controller returns counts of 0. Test also that no posts are displayed.
     */
    public function testViewMonthYearInFuture()
    {
        ApplicationTimeProvider::setDateTime(Chronos::create(2016, 2, 3, 11, 30));

        $posts = [];

        $isiteResult = new IsiteResult(1, 30, count($posts), $posts);

        $postService = $this->createMock(PostService::class);
        $postService
            ->method('getPostsByMonth')
            ->willReturn($isiteResult);

        $postService
            ->method('getOldestPostAndLatestPost')
            ->willReturn(
                [
                    PostBuilder::default()
                        ->withPublishedDate(new Chronos("2013-04-29T09:45:00.000", "Europe/London"))
                        ->build(),
                    PostBuilder::default()
                        ->withPublishedDate(new Chronos("2016-01-29T09:45:00.000", "Europe/London"))
                        ->build(),
                ]
            );

        $postService
            ->expects($this->never())
            ->method('getPostCountForMonthsInYear');

        $this->client->getContainer()->set(PostService::class, $postService);

        $this->setBlog(BlogBuilder::default()->build());

        $crawler = $this->client->request('GET', '/blogs/aboutthebbc/entries/2017/05');

        $datePickerMonths = $crawler->filterXPath('//li[contains(@class, "bbc-datepicker__box-month-name")]');

        $january = $datePickerMonths->first();
        $this->assertEquals('January (0)', trim($january->text()));

        $blogPosts = $crawler->filterXPath('//div[@itemprop="blogPost"]');
        $this->assertEquals(0, $blogPosts->count());
    }

    private function setBlog(Blog $blog)
    {
        $blogService = $this->createMock(BlogService::class);
        $blogService
            ->method('getBlogById')
            ->willReturn($blog);

        $this->client->getContainer()->set(BlogService::class, $blogService);
    }
}
