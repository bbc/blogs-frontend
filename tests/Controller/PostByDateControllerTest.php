<?php
declare(strict_types = 1);

namespace Tests\App\Controller;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Infrastructure\IsiteResult;
use App\BlogsService\Service\BlogService;
use App\BlogsService\Service\PostService;
use App\BlogsService\Service\TagService;
use App\Helper\ApplicationTimeProvider;
use Cake\Chronos\Chronos;
use Tests\App\BaseWebTestCase;

/**
 * @covers \App\Controller\PostByDateController
 */
class PostByDateControllerTest extends BaseWebTestCase
{
    public function testViewFutureMakesNoCountCalls()
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
        $postService->method('getPostsByMonth')->willReturn($isiteResult);
        $postService->expects($this->never())->method('getPostCountForMonthsInYear');

        $client->getContainer()->set(PostService::class, $postService);

        $blogService = $this->createMock(BlogService::class);
        $blogService->method('getBlogById')->willReturn($this->createMock(Blog::class));

        $client->getContainer()->set(BlogService::class, $blogService);

        $client->request('GET', '/blogs/aboutthebbc/entries/2017/05');

        $time->clearTestDateTime();
    }
}
