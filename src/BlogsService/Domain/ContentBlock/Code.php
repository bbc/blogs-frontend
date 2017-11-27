<?php

namespace BBC\BlogsService\Domain\ContentBlock;

use BBC\BlogsService\Domain\ContentBlock;

/**
 *      Content Block Code DOMAIN.
 *
 *      A DOMAIN which provides Code content blocks within a post
 *
 *      @category   Blogs
 *
 *      @copyright  Copyright (c) 2014 BBC (http://www.bbc.co.uk)
 *
 *      @link       https://confluence.dev.bbc.co.uk/display/blogs4
 *
 *      @version    1.0
 */
class Code extends ContentBlock
{
    /**
     * @var string
     */
    private $code;

    public function __construct(
        $type,
        $code
    ) {
        parent::__construct($type);

        $this->code = $code;
    }

    public function getCharacterCount()
    {
        $code = (string) $this->code;

        return strlen($code);
    }

    /**
     * Gets the value of code.
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
}
