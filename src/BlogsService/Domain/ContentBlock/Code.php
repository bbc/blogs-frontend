<?php
declare(strict_types = 1);

namespace App\BlogsService\Domain\ContentBlock;

class Code extends AbstractContentBlock
{
    /** @var string  */
    private $code;

    /** @var int */
    private $charCount;

    public function __construct(string $code)
    {
        $this->code = $code;
    }

    public function getCharacterCount(): int
    {
        if (!isset($this->charCount)) {
            $this->charCount = mb_strlen($this->code);
        }

        return $this->charCount;
    }

    public function getCode(): string
    {
        return $this->code;
    }
}
