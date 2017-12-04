<?php
declare(strict_types = 1);

namespace App\Ds\Post\ContentBlock\CodeBlock;

use App\BlogsService\Domain\ContentBlock\Code;
use App\Ds\Presenter;

class CodeBlockPresenter extends Presenter
{
    /** @var int|null */
    private $charLimit;

    /** @var Code */
    private $content;

    public function __construct(Code $content, int $limit = null, array $options = [])
    {
        parent::__construct($options);
        $this->content = $content;
        $this->charLimit = $limit;
    }

    public function getCode(): string
    {
        if ($this->charLimit === null) {
            return $this->content->getCode();
        }

        return substr($this->content->getCode(), 0, $this->charLimit);
    }
}
