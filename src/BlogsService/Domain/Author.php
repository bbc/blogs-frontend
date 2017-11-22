<?php
declare(strict_types = 1);

namespace App\BlogsService\Domain;

use App\BlogsService\Domain\ValueObject\FileID;
use App\BlogsService\Domain\ValueObject\GUID;
use App\BlogsService\Domain\ValueObject\Social;

class Author
{
    /** @var GUID */
    private $guid;

    /** @var FileID */
    private $fileId;

    /** @var string */
    private $name;

    /** @var string */
    private $role;

    /** @var string */
    private $description;

    /** @var Image */
    private $image;

    /** @var Social */
    private $social;

    public function __construct(
        GUID $guid,
        FileID $fileId,
        string $name,
        string $role,
        string $description,
        Image $image,
        Social $social
    ) {
        $this->guid = $guid;
        $this->fileId = $fileId;
        $this->name = $name;
        $this->role = $role;
        $this->description = $description;
        $this->image = $image;
        $this->social = $social;
    }

    public function getGuid(): GUID
    {
        return $this->guid;
    }

    public function getFileId(): FileID
    {
        return $this->fileId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getImage(): Image
    {
        return $this->image;
    }

    public function getSocial(): Social
    {
        return $this->social;
    }
}
