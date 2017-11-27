<?php
declare(strict_types = 1);

namespace App\Ds\Post\Content\SocialBlock;

use App\BlogsService\Domain\ContentBlock\Social;
use App\Ds\Presenter;

class SocialBlockPresenter extends Presenter
{
    /** @var Social */
    private $content;

    public function __construct(Social $content, array $options = [])
    {
        parent::__construct($options);
        $this->content = $content;
    }
}

