<?php
declare(strict_types = 1);

namespace App\Controller;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Service\BlogService;

class BlogShowController extends BaseController
{
    public function __invoke(string $blogId, BlogService $blogService)
    {
        $blogResult = $blogService->getAllBlogs();
        /** @var Blog $blog */
        $blog = $blogResult->getDomainModels()[1];
        $this->setBrandingId($blog->getBrandingId());

        return $this->renderWithChrome('blog/show.html.twig', ['blog' => $blog]);
    }
}
