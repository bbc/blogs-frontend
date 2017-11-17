<?php
declare(strict_types = 1);

namespace Tests\App\BlogsService\Repository;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

abstract class RepositoryTest extends TestCase
{
    const API_ENDPOINT = 'https://anyendpoint';

    protected function buildMockResponse(int $code)
    {
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('getStatusCode')->willReturn($code);

        return $mockResponse;
    }
}
