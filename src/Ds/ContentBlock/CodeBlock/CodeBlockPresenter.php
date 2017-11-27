<?php
declare(strict_types = 1);

namespace App\Ds\ContentBlock\CodeBlock;

use App\BlogsService\Domain\ContentBlock\Code;
use App\Ds\Presenter;

class CodeBlockPresenter extends Presenter
{
    /** @var Code */
    private $content;

    public function __construct(Code $content, array $options = [])
    {
        parent::__construct($options);
        $this->content = $content;
    }
}

