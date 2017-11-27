<?php

namespace BBC\BlogsService\Domain\ContentBlock;

use BBC\BlogsService\Domain\ContentBlock;

/**
 *      Content Block Clips DOMAIN.
 *
 *      A DOMAIN which provides Clips content blocks within a post
 *
 *      @category   Blogs
 *
 *      @copyright  Copyright (c) 2014 BBC (http://www.bbc.co.uk)
 *
 *      @link       https://confluence.dev.bbc.co.uk/display/blogs4
 *
 *      @version    1.0
 */
class Clips extends ContentBlock
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $caption;

    /**
     * @var string
     */
    private $playlistType;

    public function __construct(
        $type,

        $id,
        $url,
        $caption,
        $playlistType
    ) {
        parent::__construct($type);

        $this->id = $id;
        $this->url = $url;
        $this->caption = $caption;
        $this->playlistType = $playlistType;
    }

    /**
     * Gets the value of playlistType.
     *
     * @return string
     */
    public function getPlaylistType()
    {
        return $this->playlistType;
    }

    /**
     * Gets the value of id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gets the value of url.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Gets the value of caption.
     *
     * @return string
     */
    public function getCaption()
    {
        return $this->caption;
    }
}
