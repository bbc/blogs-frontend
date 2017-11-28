<?php
declare(strict_types = 1);

namespace App\Ds\Post\Content;

use App\BlogsService\Domain\ContentBlock\AbstractContentBlock;
use App\BlogsService\Domain\ContentBlock\Clips;
use App\BlogsService\Domain\ContentBlock\Code;
use App\BlogsService\Domain\ContentBlock\Image;
use App\BlogsService\Domain\ContentBlock\Prose;
use App\BlogsService\Domain\ContentBlock\Social;
use App\Ds\ContentBlock\ClipsBlock\ClipsBlockPresenter;
use App\Ds\ContentBlock\CodeBlock\CodeBlockPresenter;
use App\Ds\ContentBlock\ImageBlock\ImageBlockPresenter;
use App\Ds\ContentBlock\ProseBlock\ProseBlockPresenter;
use App\Ds\ContentBlock\SocialBlock\SocialBlockPresenter;
use App\Ds\Presenter;
use App\Exception\InvalidContentBlockException;

class ContentPresenter extends Presenter
{
    /** @var Presenter[] */
    private $postPresenters;

    public function __construct(array $contentBlocks, array $options = [])
    {
        parent::__construct($options);
        $this->postPresenters = array_map([$this, 'findPresenter'], $contentBlocks);
    }

    /** @return Presenter[] */
    public function getContentPresenters(): array
    {
        return $this->postPresenters;
    }

    private function findPresenter(AbstractContentBlock $contentBlock): Presenter
    {
        if ($contentBlock instanceof Prose) {
            return new ProseBlockPresenter($contentBlock);
        }

        if ($contentBlock instanceof Social) {
            return new SocialBlockPresenter($contentBlock);
        }

        if ($contentBlock instanceof Image) {
            return new ImageBlockPresenter($contentBlock);
        }

        if ($contentBlock instanceof Clips) {
            return new ClipsBlockPresenter($contentBlock);
        }

        if ($contentBlock instanceof Code) {
            return new CodeBlockPresenter($contentBlock);
        }

        throw new InvalidContentBlockException('Could not display invalid Content Block');
    }
}
