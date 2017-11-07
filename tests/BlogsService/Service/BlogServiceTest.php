<?php

namespace Tests\App\BlogsService\Service;

use App\BlogsService\Domain\IsiteEntity;
use App\BlogsService\Infrastructure\Cache\CacheInterface;
use App\BlogsService\Infrastructure\IsiteFeedResponseHandler;
use App\BlogsService\Infrastructure\IsiteResult;
use App\BlogsService\Mapper\IsiteToDomain\BlogMapper;
use App\BlogsService\Repository\BlogRepository;
use App\BlogsService\Service\BlogService;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Psr\Http\Message\ResponseInterface;
use SimpleXMLElement;

class BlogServiceTest extends TestCase
{
    /** @var BlogRepository | PHPUnit_Framework_MockObject_MockObject */
    private $mockBlogRepository;

    /** @var  IsiteFeedResponseHandler | PHPUnit_Framework_MockObject_MockObject */
    private $mockIsiteFeedResponseHandler;

    /** @var CacheInterface | PHPUnit_Framework_MockObject_MockObject */
    private $mockCache;

    public function setUp()
    {
        $this->mockBlogRepository = $this->createMock(BlogRepository::class);

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
     * @dataProvider allBlogProvider
     * @param string $responseBody
     */
    public function testGetAllBlogsRepositoryCalls(string $responseBody)
    {
        $response = new Response(200, [], $responseBody);

        $this->mockBlogRepository
            ->expects($this->once())
            ->method('getAllBlogs')
            ->willReturn($response);

        $this->mockIsiteFeedResponseHandler
            ->expects($this->once())
            ->method('getIsiteResult')
            ->with($response)
            ->willReturn($this->createMock(IsiteResult::class));

        $blogService = new BlogService(
            $this->mockBlogRepository,
            $this->mockIsiteFeedResponseHandler,
            $this->mockCache
        );

        $serviceResult = $blogService->getAllBlogs();

        $this->assertInstanceOf(IsiteResult::class, $serviceResult);
    }

    public function allBlogProvider(): array
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
