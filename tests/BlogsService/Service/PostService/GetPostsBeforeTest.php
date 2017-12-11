<?php
declare(strict_types = 1);

namespace Tests\App\BlogsService\Service\PostService;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\Post;
use App\BlogsService\Infrastructure\IsiteResult;
use App\BlogsService\Repository\PostRepository;
use App\BlogsService\Service\PostService;
use Cake\Chronos\Chronos;
use GuzzleHttp\Psr7\Response;
use PHPUnit_Framework_MockObject_MockObject;
use Tests\App\BlogsService\Service\ServiceTest;

class GetPostsBeforeTest extends ServiceTest
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
    public function testGetPostsBeforeCalls(string $responseBody)
    {
        $response = new Response(200, [], $responseBody);

        $this->mockPostRepository
            ->expects($this->once())
            ->method('getPostsBetween')
            ->willReturn($response);

        $isiteResult = $this->createMock(IsiteResult::class);
        $isiteResult->method('getDomainModels')
            ->willReturn([$this->createMock(Post::class), $this->createMock(Post::class)]);

        $this->mockIsiteFeedResponseHandler
            ->expects($this->once())
            ->method('getIsiteResult')
            ->with($response)
            ->willReturn($isiteResult);

        $postService = new PostService(
            $this->mockPostRepository,
            $this->mockIsiteFeedResponseHandler,
            $this->mockCache
        );

        $blog = $this->createMock(Blog::class);
        $blog->method('getId')->willReturn('some-id');

        $serviceResult = $postService->getPostsBefore($blog, Chronos::now());

        $this->assertInstanceOf(Post::class, $serviceResult);
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
