<?php
declare(strict_types = 1);
namespace Tests\App\ArgumentResolver;

use App\ArgumentResolver\BlogEntityByIdValueResolver;
use App\BlogsService\Domain\Blog;
use App\BlogsService\Service\BlogService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BlogEntityByIdValueResolverTest extends TestCase
{
    /** @var ArgumentResolver */
    private $resolver;

    /** @var BlogService|\PHPUnit_Framework_MockObject_MockObject */
    private $blogService;

    public function setUp()
    {
        $this->blogService = $this->createMock(BlogService::class);

        $this->resolver = new ArgumentResolver(null, [
            new BlogEntityByIdValueResolver($this->blogService),
        ]);
    }

    public function testResolveBlog()
    {
        $request = Request::create('/');
        $request->attributes->set('blogId', 'archers');
        $controller = function (Blog $blog) {
        };

        $blog = $this->createMock(Blog::class);

        $this->blogService->expects($this->once())->method('getBlogById')
            ->with('archers')
            ->willReturn($blog);

        $this->assertEquals(
            [$blog],
            $this->resolver->getArguments($request, $controller)
        );
    }

    public function testResolveOfUnfoundEntityThrows404()
    {
        $request = Request::create('/');
        $request->attributes->set('blogId', 'archers');
        $controller = function (Blog $blog) {
        };

        $this->blogService->expects($this->once())->method('getBlogById')
            ->with('archers')
            ->willReturn(null);

        $this->expectException(NotFoundHttpException::class);
        $this->expectExceptionMessage('The blog with id "archers" was not found');

        $this->resolver->getArguments($request, $controller);

        // $request = Request::create('/');
        // $request->attributes->set('pid', 'b0000001');
        // $controller = function (Programme $pid) {
        // };

        // $this->coreEntitiesService->expects($this->once())->method('findByPidFull')
        //     ->with(new Pid('b0000001'), 'Programme')
        //     ->willReturn(null);

        // $this->servicesService->expects($this->never())->method('findByPidFull');

        // $this->expectException(NotFoundHttpException::class);
        // $this->expectExceptionMessage('The item of type "' . Programme::class . '" with PID "b0000001" was not found');

        // $this->resolver->getArguments($request, $controller);
    }
}
