<?php
declare(strict_types = 1);

namespace App\Controller;

use App\BlogsService\Domain\Blog;
use Symfony\Component\HttpFoundation\Request;

class AuthorIndexAtoZController extends BlogsBaseController
{
    public function __invoke(Request $request, Blog $blog)
    {
        $this->setBlog($blog);

        return $this->renderWithChrome('author/index_atoz.html.twig');
    }
}
