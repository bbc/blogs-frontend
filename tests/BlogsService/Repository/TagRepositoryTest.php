<?php
declare(strict_types = 1);

namespace Tests\App\BlogsService\Repository;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Repository\TagRepository;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;

class TagRepositoryTest extends RepositoryTest
{
    public function testTagsByBlog404()
    {
        $mock404Response = $this->buildMockResponse(404);
        $repo = $this->createTagRepo([
            new ClientException('Error Communicating with Server', new Request('GET', 'test'), $mock404Response),
        ]);
        $result = $repo->getTagsByBlog($this->createMock(Blog::class), 1, 2, true);

        $this->assertNull($result);
    }

    /**
     * @param GuzzleException[]|ResponseInterface[] $responses
     * @return TagRepository
     */
    private function createTagRepo(array $responses):TagRepository
    {
        $mock = new MockHandler($responses);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        return new TagRepository(self::API_ENDPOINT, $client);
    }
}
