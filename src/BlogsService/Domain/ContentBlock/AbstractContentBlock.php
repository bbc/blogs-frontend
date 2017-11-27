<?php
declare(strict_types = 1);

namespace App\BlogsService\Domain\ContentBlock;

abstract class AbstractContentBlock
{
    /** @var string */
    private $type;

    public function __construct($type)
    {
        $this->type = $type;
    }

    public function getCharacterCount(): int
    {
        return 200; // items take up 200 characters by default
    }

    public function getType(): string
    {
        return $this->type;
    }
}
