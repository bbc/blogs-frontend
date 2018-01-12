<?php
declare(strict_types = 1);

namespace Tests\App\BlogsService\Repository\PostRepository;

use Cake\Chronos\Chronos;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;

class PostsBetweenParallelTest extends AbstractPostRepositoryTest
{
    public function testPostsBetweenCalls()
    {
        $mockResponse = $this->buildMockResponse(200);

        $repo = $this->createPostRepo([
            $mockResponse,
            $mockResponse,
        ]);

        $now = Chronos::now();
        $ranges = [
            'firstRequest' => ['afterDate' => $now, 'beforeDate' => $now, 'sort' => 'asc'],
            'secondRequest' => ['afterDate' => $now, 'beforeDate' => $now, 'sort' => 'asc'],
        ];

        $result = $repo->getPostsBetween('blog-id', $ranges, 1, 1, 1);

        $this->assertInternalType('array', $result);
        $this->assertCount(2, $result);
        $this->assertContainsOnly(ResponseInterface::class, $result);
        $this->assertArrayHasKey('firstRequest', $result);
        $this->assertArrayHasKey('secondRequest', $result);
    }

    /**
     * @dataProvider rangesProvider
     */
    public function testArrayKeyExceptions(array $range)
    {
        $repo = $this->createPostRepo([]);

        $this->expectException(InvalidArgumentException::class);
        $repo->getPostsBetween('blog-id', [$range], 1, 1, 1);
    }

    public function rangesProvider(): array
    {
        return [
            'invalid_array_key' => [['someArrayKey' => true]],
            'missing_array_key' => [['afterDate' => true, 'beforeDate' => true]],
            'no_array_keys' => [[true, true, true]],
        ];
    }

    public function testEmptyOn404()
    {
        $mock404Response = $this->buildMockResponse(404);

        $repo = $this->createPostRepo([
            new ClientException('Error Communicating with Server', new Request('GET', 'test'), $mock404Response),
            new ClientException('Error Communicating with Server', new Request('GET', 'test'), $mock404Response),
        ]);

        $now = Chronos::now();
        $ranges = [
            'firstRequest' => ['afterDate' => $now, 'beforeDate' => $now, 'sort' => 'asc'],
            'secondRequest' => ['afterDate' => $now, 'beforeDate' => $now, 'sort' => 'asc'],
        ];

        $result = $repo->getPostsBetween('blog-id', $ranges, 1, 1, 1);

        $this->assertNull($result['firstRequest']);
        $this->assertNull($result['secondRequest']);
    }
}
