<?php
declare(strict_types = 1);

namespace App\BlogsService\Domain\ContentBlock;

class Clips extends AbstractContentBlock
{
    /** @var string */
    private $id;

    /** @var string */
    private $url;

    /** @var string */
    private $caption;

    /** @var string */
    private $playlistType;

    public function __construct(
        string $id,
        string $url,
        string $caption,
        string $playlistType
    ) {
        $this->id = $id;
        $this->url = $url;
        $this->caption = $caption;
        $this->playlistType = $playlistType;
    }

    public function getPlaylistType(): string
    {
        return $this->playlistType;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getCaption(): string
    {
        return $this->caption;
    }
}
