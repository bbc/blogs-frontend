<?php
declare(strict_types = 1);

namespace App\BlogsService\Domain;

use App\BlogsService\Domain\ValueObject\FileID;

class Tag
{
    /** @var FileID */
    private $fileId;

    /** @var string */
    private $name;

    public function __construct(FileID $fileId, string $name)
    {
        $this->fileId = $fileId;
        $this->name = $name;
    }

    public function getFileId(): FileID
    {
        return $this->fileId;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
