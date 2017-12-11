<?php
declare(strict_types = 1);

namespace Tests\App\BlogsService\Service\PostService;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Infrastructure\IsiteResult;
use App\BlogsService\Repository\PostRepository;
use App\BlogsService\Service\PostService;
use GuzzleHttp\Psr7\Response;
use PHPUnit_Framework_MockObject_MockObject;
use Tests\App\BlogsService\Service\ServiceTest;

class GetPostCountForMonthsInYearTest extends ServiceTest
{
    /** @var PostRepository | PHPUnit_Framework_MockObject_MockObject */
    private $mockPostRepository;

    public function setUp()
    {
        $this->mockPostRepository = $this->createMock(PostRepository::class);

        $this->setUpMockResponseHandler();
        $this->setUpMockCache();
    }

    public function testGetPostCountForMonthsInYearCalls()
    {
        $responseBody = '<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema"></xs:schema>';

        $response = new Response(200, [], $responseBody);

        $responses = [
            5 => $response,
            11 => $response,
        ];

        $blog = $this->createMock(Blog::class);
        $blog->method('getId')->willReturn('some-id');

        $this->mockPostRepository
            ->expects($this->once())
            ->method('getPostsForMonthsInYear')
            ->with('some-id', 2016, [5, 11])
            ->willReturn($responses);

        $this->mockIsiteFeedResponseHandler
            ->method('getIsiteResult')
            ->willReturn($this->createMock(IsiteResult::class));

        $postService = new PostService(
            $this->mockPostRepository,
            $this->mockIsiteFeedResponseHandler,
            $this->mockCache
        );

        $result = $postService->getPostCountForMonthsInYear($blog, 2016, [5, 11]);

        $this->assertCount(2, $result);
        $this->assertArrayHasKey(5, $result);
        $this->assertArrayHasKey(11, $result);
    }
}
