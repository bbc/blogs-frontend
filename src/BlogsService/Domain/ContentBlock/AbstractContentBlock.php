<?php
declare(strict_types = 1);

namespace App\BlogsService\Domain\ContentBlock;

abstract class AbstractContentBlock
{
    abstract public function getCharacterCount(): int;
}


// This is a default value allocated to content blocks such as clips
// for the purposes of post truncation
// TODO check this when implementing post truncation
