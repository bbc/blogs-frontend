<?php
declare(strict_types=1);

namespace Tests\App\Builders;

use App\BlogsService\Domain\Author;
use App\BlogsService\Domain\ContentBlock\Prose;
use App\BlogsService\Domain\Image;
use App\BlogsService\Domain\Post;
use App\BlogsService\Domain\Tag;
use App\BlogsService\Domain\ValueObject\FileID;
use App\BlogsService\Domain\ValueObject\GUID;
use App\BlogsService\Domain\ValueObject\Social;
use Cake\Chronos\Chronos;
use Faker\Factory;

class PostBuilder implements BuilderInterface
{
    /** @var GUID */
    private $guid;

    /** @var string */
    private $forumId;

    /** @var Chronos */
    private $publishedDate;

    /** @var string */
    private $title;

    /** @var string */
    private $shortSynopsis;

    /** @var Author|null */
    private $author;

    /** @var Image|null */
    private $image;

    /** @var array */
    private $content;

    /** @var array */
    private $tags;

    public function build()
    {
        $faker = Factory::create();
        /** @var string $title */
        $title = $this->title ?? $faker->words(3, true);
        /** @var string $sentences */
        $sentences = $faker->sentences(2, true);
        /** @var array $content */
        $content = $this->content ?? [new Prose($sentences)];

        return new Post(
            $this->guid ?? new GUID($faker->uuid),
            $this->forumId ?? $faker->word,
            $this->publishedDate ?? Chronos::create($faker->year, $faker->month, $faker->dayOfMonth, $faker->numberBetween(0, 23), $faker->numberBetween(0, 59)),
            $title,
            $this->shortSynopsis ?? $faker->sentence(),
            $this->author,
            $this->image,
            $content,
            $this->tags ?? [new Tag(new FileID($faker->slug(2)), $faker->word)]
        );
    }

    public function withPublishedDate(Chronos $date)
    {
        $this->publishedDate = $date;
        return $this;
    }

    public function withTitle(string $title)
    {
        $this->title = $title;
        return $this;
    }

    public function withShortSynopsis(string $shortSynopsis)
    {
        $this->shortSynopsis = $shortSynopsis;
        return $this;
    }

    public function withAuthor(Author $author)
    {
        $this->author = $author;
        return $this;
    }

    public function withImage(Image $image)
    {
        $this->image = $image;
        return $this;
    }

    public function withContent(array $content)
    {
        $this->content = $content;
        return $this;
    }

    public function withTags(array $tags)
    {
        $this->tags = $tags;
        return $this;
    }

    public function withForumId(string $forumId)
    {
        $this->forumId = $forumId;
        return $this;
    }

    public function withGuid(GUID $guid)
    {
        $this->guid = $guid;
        return $this;
    }

    public static function default()
    {
        $faker = Factory::create();
        /** @var string $words */
        $words = $faker->words(3, true);

        $builder = new self();
        $builder->withAuthor(new Author(
            new GUID($faker->uuid),
            new FileID($faker->slug(2)),
            $faker->name(),
            $words,
            $faker->sentence(),
            new Image($faker->regexify('[0-9b-df-hj-np-tv-z]{8,15}')),
            new Social('', '', '')
        ));
        $builder->withImage(new Image($faker->regexify('[0-9b-df-hj-np-tv-z]{8,15}')));

        return $builder;
    }

    public static function minimal()
    {
        return new self();
    }
}
