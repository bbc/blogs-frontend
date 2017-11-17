<?php
declare(strict_types = 1);

namespace App\Controller;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Service\PostService;

class BlogIndexController extends BaseController
{
    public function __invoke(Blog $blog, PostService $postService)
    {
        var_dump($blog);die;

        return $this->renderWithChrome('blogindex/show.html.twig', [

        ]);
    }
}
