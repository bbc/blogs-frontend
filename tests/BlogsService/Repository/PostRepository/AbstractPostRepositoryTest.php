<?php
declare(strict_types = 1);

namespace Tests\App\BlogsService\Repository\PostRepository;

use App\BlogsService\Repository\PostRepository;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use Psr\Http\Message\ResponseInterface;
use Tests\App\BlogsService\Repository\RepositoryTest;

abstract class AbstractPostRepositoryTest extends RepositoryTest
{
    /**
     * @param GuzzleException[]|ResponseInterface[] $responses
     * @return PostRepository
     */
    protected function createPostRepo(array $responses): PostRepository
    {
        $mock = new MockHandler($responses);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        return new PostRepository(self::API_ENDPOINT, $client);
    }
}
