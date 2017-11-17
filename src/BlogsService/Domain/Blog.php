<?php
declare(strict_types = 1);

namespace App\BlogsService\Domain;

use App\BlogsService\Domain\ValueObject\Comments;
use App\BlogsService\Domain\ValueObject\FileID;
use App\BlogsService\Domain\ValueObject\GUID;
use App\BlogsService\Domain\ValueObject\Social;
use InvalidArgumentException;

class Blog extends IsiteEntity
{
    const BLOG_PREFIX = 'blogs-';

    /** @var string */
    private $id;

    /** @var string */
    private $name;

    /** @var string */
    private $shortSynopsis;

    /** @var string */
    private $description;

    /** @var Image */
    private $image;

    /** @var bool */
    private $showImageInDescription;

    /** @var string */
    private $language;

    /** @var string */
    private $istatsCountername;

    /** @var Social */
    private $social;

    /** @var Comments */
    private $comments;

    /** @var string */
    private $bbcSite;

    /** @var string */
    private $brandingId;

    /** @var Post */
    private $featuredPost;

    /** @var Module[] */
    private $modules;

    /** @var bool */
    private $isArchived;

    public function __construct(
        GUID $guid,
        FileID $fileId,
        string $id,
        string $name,
        string $shortSynopsis,
        string $description,
        bool $showImageInDescription,
        string $language,
        string $istatsCountername,
        ?string $bbcSite,
        string $brandingId,
        array $modules,
        Social $social = null,
        Comments $comments = null,
        Post $featuredPost = null,
        Image $image,
        bool $isArchived = false
    ) {
        parent::__construct($guid, $fileId);

        if (!is_bool($showImageInDescription)) {
            throw new InvalidArgumentException('showImageInDescription must be of type boolean');
        }

        if (!is_bool($isArchived)) {
            throw new InvalidArgumentException('isArchived must be of type boolean');
        }

        $this->id = $id;
        $this->name = $name;
        $this->shortSynopsis = $shortSynopsis;
        $this->description = $description;
        $this->image = $image;
        $this->showImageInDescription = $showImageInDescription;
        $this->language = $language;
        $this->istatsCountername = $istatsCountername;
        $this->social = $social;
        $this->comments = $comments;
        $this->bbcSite = $bbcSite;
        $this->brandingId = $brandingId;
        $this->featuredPost = $featuredPost;
        $this->modules = $modules;
        $this->isArchived = $isArchived;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getShortSynopsis(): string
    {
        return $this->shortSynopsis;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getImage(): Image
    {
        return $this->image;
    }

    public function getShowImageInDescription(): bool
    {
        return $this->showImageInDescription;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getIstatsCountername(): ?string
    {
        if (empty($this->istatsCountername)) {
            return null;
        }
        return $this->istatsCountername;
    }

    public function getSocial(): ?Social
    {
        return $this->social;
    }

    public function getComments(): ?Comments
    {
        return $this->comments;
    }

    public function getBbcSite(): string
    {
        return $this->bbcSite;
    }

    public function getBrandingId(): string
    {
        return $this->brandingId;
    }

    public function getFeaturedPost(): ?Post
    {
        return $this->featuredPost;
    }

    /** @return Module[] */
    public function getModules(): array
    {
        return $this->modules;
    }

    public function getIsArchived(): bool
    {
        return $this->isArchived;
    }
}
