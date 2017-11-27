<?php

namespace BBC\BlogsService\Domain\ContentBlock;

use BBC\BlogsService\Domain\ContentBlock;

/**
 *      Content Block Prose DOMAIN.
 *
 *      A DOMAIN which provides Prose content blocks within a post
 *
 *      @category   Blogs
 *
 *      @copyright  Copyright (c) 2014 BBC (http://www.bbc.co.uk)
 *
 *      @link       https://confluence.dev.bbc.co.uk/display/blogs4
 *
 *      @version    1.0
 */
class Prose extends ContentBlock
{
    /**
     * @var string
     */
    private $prose;

    public function __construct(
        $type,
        $prose
    ) {
        parent::__construct($type);

        $this->prose = $prose;
    }

    public function getCharacterCount()
    {
        $prose = (string) $this->prose;

        return strlen(strip_tags($prose));
    }

    /**
     * Gets the value of prose.
     *
     * @return string
     */
    public function getProse()
    {
        return $this->prose;
    }
}
