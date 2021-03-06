<?php
declare(strict_types=1);

namespace App\BlogsService\Domain;

use App\BlogsService\Domain\ContentBlock\AbstractContentBlock;
use App\BlogsService\Domain\ContentBlock\Clips;
use App\BlogsService\Domain\ValueObject\GUID;
use Cake\Chronos\Chronos;

class Post
{
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

    /** @var bool */
    private $hasVideo;

    /** @var string */
    private $forumId;

    /** @var GUID */
    private $guid;

    /** @var Chronos */
    private $displayDate;

    public function __construct(
        GUID $guid,
        string $forumId,
        Chronos $publishedDate,
        Chronos $displayDate,
        string $title,
        string $shortSynopsis,
        ?Author $author = null,
        ?Image $image = null,
        array $content = [],
        array $tags = []
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
        $this->displayDate = $displayDate;
    }

    public function getForumId(): string
    {
        return $this->forumId;
    }

    public function getGuid(): GUID
    {
        return $this->guid;
    }

    /**
     * The user-input published date as returned by iSite without any messing around
     *
     * @return Chronos
     */
    public function getPublishedDate(): Chronos
    {
        return $this->publishedDate;
    }

    /**
     * The user-input published date re-jigged to assume that the timezone the user *meant* was
     * Europe/London. This results in a correct date output when fed to localtime functions
     *
     * @return Chronos
     */
    public function getDisplayDate(): Chronos
    {
        return $this->displayDate;
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

    /** @return AbstractContentBlock[] */
    public function getContent(): array
    {
        return $this->content;
    }

    /** @return Tag[] */
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
