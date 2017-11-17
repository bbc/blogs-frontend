<?php
declare(strict_types=1);

namespace App\BlogsService\Domain;

use App\BlogsService\Domain\ValueObject\FileID;
use App\BlogsService\Domain\ValueObject\GUID;
use App\BlogsService\Domain\ValueObject\Image;
use DateTimeImmutable;

class Post extends IsiteEntity
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


    public function __construct(
        GUID $guid,
        FileID $fileId,
        string $forumId, //TODO check with new comments
        DateTimeImmutable $publishedDate,
        string $title,
        string $shortSynopsis,
        Author $author = null,
        Image $image = null,
        array $content = null,
        array $tags = null
    ) {
        parent::__construct($guid, $fileId);

        $this->forumId = $forumId;
        $this->publishedDate = $publishedDate;
        $this->title = $title;
        $this->shortSynopsis = $shortSynopsis;
        $this->author = $author;
        $this->image = $image;
        $this->content = $content;
        $this->tags = $tags;
    }

//    public function getForumId($blog)
//    {
//        //prefix is "blog_BLOGNAME"
//        $forumIdPrefix = str_replace("-", "_", $blog->getId());
//
//        //full forumid looks like this blog_internet_4898c498-f08e-4242-9cd6-5229536f9e88
//        return $forumIdPrefix . $this->forumId;
//    }

    public function getPublishedDate(): string
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

//    public function getAuthor(): Author
//    {
//        return $this->author;
//    }

    public function getImage(): Image
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
            $this->hasVideo = false;
            $contentBlocks = $this->getContent();
            foreach ($contentBlocks as $content) {
                if ($content instanceof Clips) {
                    $this->hasVideo = true;
                    break;
                }
            }
        }

        return $this->hasVideo;
    }
}
