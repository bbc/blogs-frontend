<?php
declare(strict_types = 1);

namespace Tests\App\Builders;

use App\BlogsService\Domain\Author;
use App\BlogsService\Domain\Image;
use App\BlogsService\Domain\ValueObject\FileID;
use App\BlogsService\Domain\ValueObject\GUID;
use App\BlogsService\Domain\ValueObject\Social;
use Faker\Factory;

class AuthorBuilder implements BuilderInterface
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

    public function build()
    {
        $faker = Factory::create();

        return new Author(
            $this->guid ?? new GUID($faker->uuid),
            $this->fileId ?? new FileID($faker->slug(3)),
            $this->name ?? $faker->name(),
            $this->role ?? (string) $faker->words(2, true),
            $this->description ?? $faker->sentence(),
            $this->image ?? new Image($faker->regexify('[0-9b-df-hj-np-tv-z]{8,15}')),
            $this->social ?? new Social('', '', '')
        );
    }

    public function withGuid(GUID $guid)
    {
        $this->guid = $guid;
        return $this;
    }

    public function withFileId(FileID $fileID)
    {
        $this->fileId = $fileID;
        return $this;
    }

    public function withName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    public function withRole(string $role)
    {
        $this->role = $role;
        return $this;
    }

    public function withDescription(string $description)
    {
        $this->description = $description;
        return $this;
    }

    public function withImage(Image $image)
    {
        $this->image = $image;
        return $this;
    }

    public function withSocial(Social $social)
    {
        $this->social = $social;
        return $this;
    }

    public static function default()
    {
        return new self();
    }
}
