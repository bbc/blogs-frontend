<?php
declare(strict_types = 1);

namespace App\Controller;

use App\BlogsService\Domain\Blog;
use Symfony\Component\HttpFoundation\Request;

class AuthorIndexAtoZController extends BlogsBaseController
{
    public function __invoke(Blog $blog)
    {
        $this->setBlog($blog);
        $this->pageContextHelper()->setDescription(
            'A-Z listing of authors on the BBC\'s "' . $blog->getName() . '"" blog',
        );

        $this->analyticsHelper()->setChapterOneVariable('list-authors');

        return $this->renderWithChrome(
            'author/index.html.twig',
            ['showAZ' => true]
        );
    }
}
