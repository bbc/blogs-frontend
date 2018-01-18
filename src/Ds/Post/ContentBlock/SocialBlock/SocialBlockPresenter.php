<?php
declare(strict_types = 1);

namespace App\Ds\Post\ContentBlock\SocialBlock;

use App\BlogsService\Domain\ContentBlock\Social;
use App\Ds\Presenter;

class SocialBlockPresenter extends Presenter
{
    /** @var Social */
    private $content;

    /** @var string */
    private $randId;

    /** @var int */
    private static $COUNTER = 0;

    public function __construct(Social $content, array $options = [])
    {
        parent::__construct($options);
        $this->content = $content;
    }

    public function getAlt(): string
    {
        return $this->content->getAlt() ? $this->content->getAlt() : $this->content->getLink();
    }

    public function getLink(): string
    {
        return $this->content->getLink();
    }

    public function getContainerId(): string
    {
        if (!isset($this->randId)) {
            $this->randId = 'third-party-' . self::getSocialBlockCount();
        }

        return $this->randId;
    }

    private static function getSocialBlockCount(): int
    {
        return self::$COUNTER++;
    }
}
