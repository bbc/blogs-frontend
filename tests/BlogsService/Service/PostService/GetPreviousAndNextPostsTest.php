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

class GetPreviousAndNextPostsTest extends ServiceTest
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
     * @dataProvider responseProvider
     */
    public function testGetPreviousAndNextPostsCalls(?Response $previous, ?Response $next, array $domainModels, string $type)
    {
        $responses = [
            'previousPost' => $previous,
            'nextPost' => $next,
        ];

        $blog = $this->createMock(Blog::class);
        $blog->method('getId')->willReturn('some-id');

        $this->mockPostRepository
            ->expects($this->once())
            ->method('getPostsBetween')
            ->willReturn($responses);

        $mockIsiteResult = $this->createConfiguredMock(IsiteResult::class, [
            'getDomainModels' => $domainModels,
        ]);

        $this->mockIsiteFeedResponseHandler
            ->method('getIsiteResult')
            ->willReturn($mockIsiteResult);

        $postService = new PostService(
            $this->mockPostRepository,
            $this->mockIsiteFeedResponseHandler,
            $this->mockCache
        );

        $result = $postService->getPreviousAndNextPosts($blog, Chronos::now());

        $this->assertCount(2, $result);
        $this->assertContainsOnly($type, $result);
    }

    public function responseProvider(): array
    {
        $responseBody = '<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema"></xs:schema>';
        $response = new Response(200, [], $responseBody);

        return [
            'posts' => [$response, $response, [$this->createMock(Post::class)], Post::class],
            'noPosts' => [null, null, [], 'null'],
        ];
    }
}
