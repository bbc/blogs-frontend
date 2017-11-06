<?php
declare(strict_types = 1);

namespace App\Controller;

class HomeController extends BaseController
{
    public function __invoke()
    {
        return $this->renderWithChrome('home/show.html.twig');
    }
}
