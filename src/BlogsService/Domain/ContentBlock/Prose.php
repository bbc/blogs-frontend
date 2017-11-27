<?php
declare(strict_types = 1);

namespace App\BlogsService\Domain\ContentBlock;

class Prose extends AbstractContentBlock
{
    /** @var string */
    private $prose;

    public function __construct(string $prose)
    {
        $this->prose = $prose;
    }

    public function getCharacterCount(): int
    {
        $prose = (string) $this->prose;

        return strlen(strip_tags($prose));
    }

    public function getProse(): string
    {
        return $this->prose;
    }
}
