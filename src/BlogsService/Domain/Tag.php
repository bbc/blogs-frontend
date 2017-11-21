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

    /** @var string */
    private $id;

    public function __construct(FileID $fileId, string $name)
    {
        $this->fileId = $fileId;
        $this->name = $name;
        $this->id = str_replace('tag-', '', (string) $fileId);
    }

    public function getFileId(): FileID
    {
        return $this->fileId;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
