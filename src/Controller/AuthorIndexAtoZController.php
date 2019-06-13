<?php
declare(strict_types = 1);

namespace App\Controller;

use App\BlogsService\Domain\Blog;
use Symfony\Component\HttpFoundation\Request;

class AuthorIndexAtoZController extends BlogsBaseController
{
    public function __invoke(Blog $blog)
    {
        $pageMetadata = $this->pageMetadataHelper()->makePageMetadata(
            'A-Z listing of authors on the BBC\'s "' . $blog->getName() . '"" blog',
            $blog
        );

        $analyticsLabels = $this->atiAnalyticsHelper()->makeLabels(
            'list-authors',
            $blog
        );

        return $this->renderBlogPage(
            'author/index.html.twig', $analyticsLabels, $pageMetadata, $blog, ['showAZ' => true]
        );
    }
}
