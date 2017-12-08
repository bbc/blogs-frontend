<?php
declare(strict_types = 1);

namespace App\Controller;

use App\BlogsService\Domain\Blog;
use Cake\Chronos\Date;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostByDateRedirectController extends AbstractController
{
    public function __invoke(Blog $blog)
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
