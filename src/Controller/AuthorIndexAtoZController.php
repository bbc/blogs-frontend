<?php
declare(strict_types = 1);

namespace App\Controller;

use App\BlogsService\Domain\Blog;

class AuthorIndexAtoZController extends BlogsBaseController
{
    public function __invoke(Blog $blog)
    {
        $pageMetadata = $this->pageMetadataHelper()->makePageMetadata(
            'Alphabetical listing of authors on the BBC\'s ' . $this->pageMetadataHelper()->blogNameForDescription($blog),
            $blog
        );

        $analyticsLabels = $this->atiAnalyticsHelper()->makeLabels(
            'list-authors',
            $blog
        );

        return $this->renderBlogPage(
            'author/index.html.twig',
            $analyticsLabels,
            $pageMetadata,
            $blog,
            ['showAZ' => true]
        );
    }
}
