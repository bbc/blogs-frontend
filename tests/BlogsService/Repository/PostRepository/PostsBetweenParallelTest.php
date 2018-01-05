<?php
declare(strict_types = 1);

namespace Tests\App\BlogsService\Repository\PostRepository;

use Cake\Chronos\Chronos;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;

class PostsBetweenParallelTest extends AbstractPostRepositoryTest
{
    public function testPostsBetweenParallelCalls()
    {
//        $mockResponse = $this->buildMockResponse(200);
//
//        $repo = $this->createPostRepo([
//            new Request('GET', 'test', $mockResponse)
//        ]);
    }


    public function testPostsAfterEmptyOn404()
    {
//        $mock404Response = $this->buildMockResponse(404);
//
//        $repo = $this->createPostRepo([
//            new ClientException('Error Communicating with Server', new Request('GET', 'test'), $mock404Response),
//        ]);
//
//        $now = Chronos::now();
//
//        $ranges = [
//            'someindex' => [
//                'afterDate' => $now,
//                'beforeDate' => $now,
//                'sort' => 'asc',
//            ]
//        ];
//
//        $result = $repo->getPostsBetweenParallel('blog-id', $ranges,1, 1, 1);
//
//        dump($result);die;
//
//        $this->assertContainsOnly('null', $result);
    }
}
