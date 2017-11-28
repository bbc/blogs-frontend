<?php
declare(strict_types = 1);

namespace App\Ds\ContentBlock\SocialBlock;

use App\BlogsService\Domain\ContentBlock\Social;
use App\Ds\Presenter;

class SocialBlockPresenter extends Presenter
{
    /** @var Social */
    private $content;

    /** @var string */
    private $randId;

    public function __construct(Social $content, array $options = [])
    {
        parent::__construct($options);
        $this->content = $content;
    }

    public function getAlt(): string
    {
        return $this->content->getAlt() ?? $this->content->getLink();
    }

    public function getLink(): string
    {
        return $this->content->getLink();
    }

    public function getContainerId(): string
    {
        if (!isset($this->randId)) {
            $this->randId = 'third-party-' . (string) microtime(true) * 10000;
        }

        return $this->randId;
    }
}
