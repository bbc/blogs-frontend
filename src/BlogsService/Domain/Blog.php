<?php
declare(strict_types = 1);

namespace App\BlogsService\Domain;

use App\BlogsService\Domain\Module\ModuleInterface;
use App\BlogsService\Domain\ValueObject\Comments;
use App\BlogsService\Domain\ValueObject\Social;
use InvalidArgumentException;

class Blog
{
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

    /** @var Social */
    private $social;

    /** @var string */
    private $bbcSite;

    /** @var string */
    private $brandingId;

    /** @var Post */
    private $featuredPost;

    /** @var ModuleInterface[] */
    private $modules;

    /** @var bool */
    private $isArchived;

    /** @var string */
    private $commentsApiKey;

    public function __construct(
        string $id,
        string $name,
        string $shortSynopsis,
        string $description,
        bool $showImageInDescription,
        string $language,
        string $bbcSite,
        string $brandingId,
        array $modules,
        Social $social,
        ?string $commentsApiKey,
        Post $featuredPost = null,
        Image $image,
        bool $isArchived = false
    ) {
        if (!\is_bool($showImageInDescription)) {
            throw new InvalidArgumentException('showImageInDescription must be of type boolean');
        }

        if (!\is_bool($isArchived)) {
            throw new InvalidArgumentException('isArchived must be of type boolean');
        }

        $this->id = $id;
        $this->name = $name;
        $this->shortSynopsis = $shortSynopsis;
        $this->description = $description;
        $this->image = $image;
        $this->showImageInDescription = $showImageInDescription;
        $this->language = $language;
        $this->social = $social;
        $this->bbcSite = $bbcSite;
        $this->brandingId = $brandingId;
        $this->featuredPost = $featuredPost;
        $this->modules = $modules;
        $this->isArchived = $isArchived;
        $this->commentsApiKey = $commentsApiKey;
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

    public function getSocial(): Social
    {
        return $this->social;
    }

    public function hasCommentsEnabled(): bool
    {
        return !empty($this->commentsApiKey);
    }

    public function getCommentsApiKey(): string
    {
        return $this->commentsApiKey ? $this->commentsApiKey : '';
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

    /** @return ModuleInterface[] */
    public function getModules(): array
    {
        return $this->modules;
    }

    public function getIsArchived(): bool
    {
        return $this->isArchived;
    }
}
