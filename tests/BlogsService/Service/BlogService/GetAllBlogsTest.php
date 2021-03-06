<?php
declare(strict_types = 1);

namespace Tests\App\BlogsService\Service\BlogService;

use App\BlogsService\Infrastructure\IsiteResult;
use App\BlogsService\Repository\BlogRepository;
use App\BlogsService\Service\BlogService;
use GuzzleHttp\Psr7\Response;
use PHPUnit_Framework_MockObject_MockObject;
use Tests\App\BlogsService\Service\ServiceTest;

class GetAllBlogsTest extends ServiceTest
{
    /** @var BlogRepository | PHPUnit_Framework_MockObject_MockObject */
    private $mockBlogRepository;

    public function setUp()
    {
        $this->mockBlogRepository = $this->createMock(BlogRepository::class);

        $this->setUpMockResponseHandler();
        $this->setUpMockCache();
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
