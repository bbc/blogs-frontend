<?php
declare(strict_types = 1);

namespace App\ArgumentResolver;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Service\BlogService;
use Generator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class BlogEntityByIdValueResolver implements ArgumentValueResolverInterface
{
    /** @var BlogService */
    private $blogService;

    public function __construct(BlogService $blogService)
    {
        $this->blogService = $blogService;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return ($request->attributes->has('blogId') && $argument->getType() == Blog::class && !$argument->isVariadic());
    }

    public function resolve(Request $request, ArgumentMetadata $argument): Generator
    {
        yield $this->blogService->getBlogById($request->attributes->get('blogId'))->getDomainModels()[0];
    }
}
