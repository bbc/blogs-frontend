<?php
declare(strict_types = 1);

namespace App\Ds\SidebarModule;

use App\BlogsService\Domain\Blog;
use App\Ds\Presenter;

class UpdatesPresenter extends Presenter
{
    /** @var Blog */
    private $blog;

    public function __construct(Blog $blog, array $options = [])
    {
        parent::__construct($options);

        $this->blog = $blog;
    }

    public function getBlogId(): string
    {
        return $this->blog->getId();
    }
}
