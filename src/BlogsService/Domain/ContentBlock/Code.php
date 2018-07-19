<?php
declare(strict_types = 1);

namespace App\BlogsService\Domain\ContentBlock;

class Code extends AbstractContentBlock
{
    /** @var string  */
    private $code;

    /** @var int|null */
    private $charCount;

    public function __construct(string $code)
    {
        $this->code = $code;
    }

    public function getCharacterCount(): int
    {
        if ($this->charCount === null) {
            $this->charCount = mb_strlen($this->code) ?: 0;
//            $this->charCount = $this->charCount ? $this->charCount : 0;
        }

        return $this->charCount;
    }

    public function getCode(): string
    {
        return $this->code;
    }
}
