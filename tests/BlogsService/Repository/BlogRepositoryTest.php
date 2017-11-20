<?php
declare(strict_types = 1);

namespace Tests\App\BlogsService\Repository;

use App\BlogsService\Infrastructure\IsiteResultException;
use App\BlogsService\Repository\BlogRepository;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;

class BlogRepositoryTest extends RepositoryTest
{
    public function testAllBlogsQueryIsBuiltCorrectlyAndCallsCorrectUrl()
    {
        $allBlogsUrl = self::API_ENDPOINT . '/search?q={"searchChildrenOfProject":"blogs","fileType":"blogsmetadata","query":{"or":[["blog-name","contains","*"]]},"sort":[{"elementPath":"\/*:form\/*:metadata\/*:blog-name"}],"depth":"0","unfiltered":true}';

        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->once())
            ->method('request')
            ->with('GET', $allBlogsUrl)
            ->willReturn($this->createMock(ResponseInterface::class));
        $repo = new BlogRepository(self::API_ENDPOINT, $client);
        $repo->getAllBlogs();
    }

    public function testAllBlogsReturnsIsiteResult()
    {
        $repo = $this->createBlogRepo([$this->createMock(ResponseInterface::class)]);

        $this->assertInstanceOf(ResponseInterface::class, $repo->getAllBlogs());
    }

    public function testAllBlogsEmptyOn404()
    {
        $mock404Response = $this->buildMockResponse(404);

        $repo = $this->createBlogRepo([
            new ClientException('Error Communicating with Server', new Request('GET', 'test'), $mock404Response),
        ]);

        $result = $repo->getAllBlogs();

        $this->assertNull($result);
    }

    /**
     * @dataProvider exceptionsTestDataProvider
     * @param GuzzleException $guzzleException
     */
    public function testAllBlogsExceptions(GuzzleException $guzzleException)
    {
        $repo = $this->createBlogRepo([$guzzleException]);

        $this->expectException(IsiteResultException::class);
        $this->expectExceptionMessage('There was an error retrieving data from iSite.');

        $repo->getAllBlogs();
    }

    public function exceptionsTestDataProvider(): array
    {
        return [
            '4xx' => [
                new ClientException(
                    'Error Communicating with Server',
                    new Request('GET', 'test'),
                    $this->buildMockResponse(418)
                ),
            ],
            '5xx' => [
                new ServerException(
                    'Error Communicating with Server',
                    new Request('GET', 'test'),
                    $this->buildMockResponse(500)
                ),
            ],
        ];
    }

    public function testGetBlogByIdEmptyOn404()
    {
        $mock404Response = $this->buildMockResponse(404);

        $repo = $this->createBlogRepo([
            new ClientException('Error Communicating with Server', new Request('GET', 'test'), $mock404Response),
        ]);

        $result = $repo->getBlogById('someid');

        $this->assertNull($result);
    }

    /**
     * @param GuzzleException[]|ResponseInterface[] $responses
     * @return BlogRepository
     */
    private function createBlogRepo(array $responses): BlogRepository
    {
        $mock = new MockHandler($responses);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        return new BlogRepository(self::API_ENDPOINT, $client);
    }
}
