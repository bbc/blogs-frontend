<?php
declare(strict_types = 1);

namespace Tests\App\BlogsService\Service\PostService;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Infrastructure\IsiteResult;
use App\BlogsService\Repository\PostRepository;
use App\BlogsService\Service\PostService;
use DateTimeImmutable;
use GuzzleHttp\Psr7\Response;
use PHPUnit_Framework_MockObject_MockObject;
use Tests\App\BlogsService\Service\ServiceTest;

class GetPostsAfterTest extends ServiceTest
{
    /** @var PostRepository | PHPUnit_Framework_MockObject_MockObject */
    private $mockPostRepository;

    public function setUp()
    {
        $this->mockPostRepository = $this->createMock(PostRepository::class);

        $this->setUpMockResponseHandler();
        $this->setUpMockCache();
    }

    /**
     * @dataProvider postProvider
     * @param string $responseBody
     */
    public function testGetPostsAfterCalls(string $responseBody)
    {
        $response = new Response(200, [], $responseBody);

        $this->mockPostRepository
            ->expects($this->once())
            ->method('getPostsAfter')
            ->willReturn($response);

        $this->mockIsiteFeedResponseHandler
            ->expects($this->once())
            ->method('getIsiteResult')
            ->with($response)
            ->willReturn($this->createMock(IsiteResult::class));

        $postService = new PostService(
            $this->mockPostRepository,
            $this->mockIsiteFeedResponseHandler,
            $this->mockCache
        );

        $blog = $this->createMock(Blog::class);
        $blog->method('getId')->willReturn('some-id');

        $serviceResult = $postService->getPostsAfter($blog, new DateTimeImmutable(), new DateTimeImmutable());

        $this->assertInstanceOf(IsiteResult::class, $serviceResult);
    }

    public function postProvider(): array
    {
        return [
            'results' => [
                '<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema"></xs:schema>',
            ],
            'no-results' => [
                '',
            ],
        ];
    }
}
