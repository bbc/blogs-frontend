<?php
declare(strict_types = 1);

namespace Tests\App\BlogsService\Service\TagService;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Infrastructure\Cache\CacheInterface;
use App\BlogsService\Infrastructure\IsiteFeedResponseHandler;
use App\BlogsService\Infrastructure\IsiteResult;
use App\BlogsService\Repository\TagRepository;
use App\BlogsService\Service\TagService;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;

class TagsByBlogTest extends TestCase
{
    /** @var TagRepository | PHPUnit_Framework_MockObject_MockObject */
    private $mockTagRepository;

    /** @var  IsiteFeedResponseHandler | PHPUnit_Framework_MockObject_MockObject */
    private $mockIsiteFeedResponseHandler;

    /** @var CacheInterface | PHPUnit_Framework_MockObject_MockObject */
    private $mockCache;

    public function setUp()
    {
        $this->mockTagRepository = $this->createMock(TagRepository::class);

        $this->mockIsiteFeedResponseHandler = $this->createMock(IsiteFeedResponseHandler::class);

        $this->mockCache = $this->createMock(CacheInterface::class);
        $this->mockCache
            ->expects($this->once())
            ->method('getOrSet')
            ->will($this->returnCallback(
                function (string $key, $ttl, callable $function, array $arguments = []) {
                    return $function(...$arguments);
                }
            ));
    }

    /**
     * @dataProvider allTagProvider
     * @param string $responseBody
     */
    public function testTagsByBlogRepositoryCalls(string $responseBody)
    {
        $response = new Response(200, [], $responseBody);

        $this->mockTagRepository
            ->expects($this->once())
            ->method('getTagsByBlog')
            ->willReturn($response);

        $this->mockIsiteFeedResponseHandler
            ->expects($this->once())
            ->method('getIsiteResult')
            ->with($response)
            ->willReturn($this->createMock(IsiteResult::class));

        $tagService = new TagService(
            $this->mockTagRepository,
            $this->mockIsiteFeedResponseHandler,
            $this->mockCache
        );

        $mockBlog = $this->createConfiguredMock(Blog::class, ['getId' => 'blogid']);

        $serviceResult = $tagService->getTagsByBlog($mockBlog, 1, 2, false);

        $this->assertInstanceOf(IsiteResult::class, $serviceResult);
    }

    public function allTagProvider(): array
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
