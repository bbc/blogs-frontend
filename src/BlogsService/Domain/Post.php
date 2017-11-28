<?php
declare(strict_types=1);

namespace App\BlogsService\Domain;

use App\BlogsService\Domain\ContentBlock\Clips;
use App\BlogsService\Domain\ValueObject\GUID;
use DateTimeImmutable;

class Post
{
    /** @var DateTimeImmutable */
    private $publishedDate;

    /** @var string */
    private $title;

    /** @var string */
    private $shortSynopsis;

    /** @var Author */
    private $author;

    /** @var Image */
    private $image;

    /** @var array */
    private $content;

    /** @var array */
    private $tags;

    /** @var bool */
    private $hasVideo;

    /** @var string */
    private $forumId;

    /** @var GUID */
    private $guid;

    public function __construct(
        GUID $guid,
        string $forumId,
        DateTimeImmutable $publishedDate,
        string $title,
        string $shortSynopsis,
        Author $author = null,
        Image $image = null,
        array $content = null,
        array $tags = null
    ) {
        $this->guid = $guid;
        $this->forumId = $forumId;
        $this->publishedDate = $publishedDate;
        $this->title = $title;
        $this->shortSynopsis = $shortSynopsis;
        $this->author = $author;
        $this->image = $image;
        $this->content = $content;
        $this->tags = $tags;
    }

    public function getGuid(): GUID
    {
        return $this->guid;
    }

    public function getPublishedDate(): DateTimeImmutable
    {
        return $this->publishedDate;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getShortSynopsis(): string
    {
        return $this->shortSynopsis;
    }

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function getContent(): array
    {
        return $this->content;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function hasVideo(): bool
    {
        if (!isset($this->hasVideo)) {
            $this->hasVideo = $this->contentContainsClip();
        }

        return $this->hasVideo;
    }

    private function contentContainsClip(): bool
    {
        $contentBlocks = $this->getContent();
        foreach ($contentBlocks as $content) {
            if ($content instanceof Clips) {
                return true;
            }
        }
        return false;
    }
}
