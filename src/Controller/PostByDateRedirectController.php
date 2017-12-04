<?php
declare(strict_types = 1);

namespace App\Controller;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Service\PostService;
use App\Ds\Molecule\DatePicker\DatePicker;
use App\Ds\Molecule\Paginator\PaginatorPresenter;
use Cake\Chronos\Date;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;

class PostByDateRedirectController extends BlogsBaseController
{
    public function __invoke(Request $request, Blog $blog)
    {
        $now = new Date('now');

        return $this->redirectToRoute(
            'posts_year_month',
            [
                'blogId' => $blog->getId(),
                'year' => $now->format('Y'),
                'month' => $now->format('m'),
            ]
        );
    }
}
