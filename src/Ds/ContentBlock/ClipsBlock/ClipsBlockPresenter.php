<?php
declare(strict_types = 1);

namespace App\Ds\ContentBlock\ClipsBlock;

use App\BlogsService\Domain\ContentBlock\Clips;
use App\Ds\Presenter;

class ClipsBlockPresenter extends Presenter
{
    /** @var Clips */
    private $content;

    public function __construct(Clips $content, array $options = [])
    {
        parent::__construct($options);
        $this->content = $content;
    }
}

