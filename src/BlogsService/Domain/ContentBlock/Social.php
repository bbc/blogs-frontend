<?php

namespace BBC\BlogsService\Domain\ContentBlock;

use BBC\BlogsService\Domain\ContentBlock;

/**
 *      Content Block Social DOMAIN.
 *
 *      A DOMAIN which provides Social content blocks within a post
 *
 *      @category   Blogs
 *
 *      @copyright  Copyright (c) 2014 BBC (http://www.bbc.co.uk)
 *
 *      @link       https://confluence.dev.bbc.co.uk/display/blogs4
 *
 *      @version    1.0
 */
class Social extends ContentBlock
{
    /**
     * @var string
     */
    private $link;

    /**
     * @var string
     */
    private $alt;

    public function __construct(
        $type,
        $link,
        $alt
    ) {
        parent::__construct($type);

        $this->link = $link;
        $this->alt = $alt;
    }

    /**
     * Gets the value of link.
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Gets the value of alt.
     *
     * @return string
     */
    public function getAlt()
    {
        return $this->alt;
    }
}
