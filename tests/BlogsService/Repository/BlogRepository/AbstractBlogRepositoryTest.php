<?php
declare(strict_types = 1);

namespace Tests\App\BlogsService\Repository\BlogRepository;

use App\BlogsService\Repository\BlogRepository;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use Psr\Http\Message\ResponseInterface;
use Tests\App\BlogsService\Repository\RepositoryTest;

abstract class AbstractBlogRepositoryTest extends RepositoryTest
{
    /**
     * @param GuzzleException[]|ResponseInterface[] $responses
     * @return BlogRepository
     */
    protected function createBlogRepo(array $responses): BlogRepository
    {
        $mock = new MockHandler($responses);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        return new BlogRepository(self::API_ENDPOINT, $client);
    }
}
