<?php
declare(strict_types = 1);

namespace Tests\App\BlogsService\Repository\TagRepository;

use App\BlogsService\Repository\TagRepository;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use Psr\Http\Message\ResponseInterface;
use Tests\App\BlogsService\Repository\RepositoryTest;

abstract class AbstractTagRepositoryTest extends RepositoryTest
{
    /**
     * @param GuzzleException[]|ResponseInterface[] $responses
     * @return TagRepository
     */
    protected function createTagRepo(array $responses): TagRepository
    {
        $mock = new MockHandler($responses);

        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        return new TagRepository(self::API_ENDPOINT, $client);
    }
}
