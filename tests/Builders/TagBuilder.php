<?php
declare(strict_types = 1);

namespace Tests\App\Builders;

use App\BlogsService\Domain\Tag;
use App\BlogsService\Domain\ValueObject\FileID;
use Faker\Factory;

class TagBuilder implements BuilderInterface
{
    /** @var FileID */
    private $fileId;

    /** @var string */
    private $name;

    public function build()
    {
        $faker = Factory::create();

        return new Tag(
            $this->fileId ?? new FileID($faker->slug(3)),
            $this->name ?? $faker->word
        );
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

    public static function default()
    {
        return new self();
    }
}
