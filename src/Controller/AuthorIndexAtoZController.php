<?php
declare(strict_types = 1);

namespace App\Controller;

use App\BlogsService\Domain\Blog;
use Symfony\Component\HttpFoundation\Request;

class AuthorIndexAtoZController extends BlogsBaseController
{
    public function __invoke(Blog $blog)
    {
        $pageContext = $this->pageContextHelper()->makePageContext(
            'A-Z listing of authors on the BBC\'s "' . $blog->getName() . '"" blog',
            $blog
        );

        $analyticsLabels = $this->analyticsHelper()->makeLabels(
            'list-authors',
            $blog
        );

        return $this->renderWithChrome(
            $pageContext,
            $analyticsLabels,
            'author/index.html.twig',
            ['showAZ' => true]
        );
    }
}
