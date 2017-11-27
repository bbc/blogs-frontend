<?php

namespace BBC\BlogsService\Domain\ContentBlock;

use BBC\BlogsService\Domain\ContentBlock;
use BBC\BlogsService\Domain\ValueObject\Image as ValueObjectImage;

/**
 *      Content Block image DOMAIN.
 *
 *      A DOMAIN which provides Image content blocks within a post
 *
 *      @category   Blogs
 *
 *      @copyright  Copyright (c) 2014 BBC (http://www.bbc.co.uk)
 *
 *      @link       https://confluence.dev.bbc.co.uk/display/blogs4
 *
 *      @version    1.0
 */
class Image extends ContentBlock
{
    /**
     * @var Image
     */
    private $image;

    /**
     * @var string
     */
    private $caption;

    /**
     * @var bool
     */
    private $hasCaption;

    public function __construct(
        $type,
        ValueObjectImage $image = null,
        $caption = null
    ) {
        parent::__construct($type);

        $this->image    = $image;
        $this->caption  = $caption;

        if (empty($caption)) {
            $this->hasCaption = false;
        } else {
            $this->hasCaption = true;
        }
    }

    /**
     * Gets the value of image.
     *
     * @return Image
     */
    public function getImage()
    {
        return $this->image;
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

    /**
     * Gets the value of hasCaption.
     *
     * @return bool
     */
    public function hasCaption()
    {
        return $this->hasCaption;
    }
}
