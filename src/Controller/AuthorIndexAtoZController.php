<?php
declare(strict_types = 1);

namespace App\Controller;

use App\BlogsService\Domain\Blog;
use Symfony\Component\HttpFoundation\Request;

class AuthorIndexAtoZController extends BlogsBaseController
{
    public function __invoke(Request $request, Blog $blog)
    {
        $this->setIstatsPageType('author_indexatoz');
        $this->setBlog($blog);
        $this->counterName = 'authors';

        return $this->renderWithChrome('author/index.html.twig', ['showAZ' => true]);
    }
}
