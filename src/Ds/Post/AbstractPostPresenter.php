<?php
declare(strict_types = 1);

namespace App\Ds\Post;

use App\BlogsService\Domain\ContentBlock\AbstractContentBlock;
use App\BlogsService\Domain\ContentBlock\Clips;
use App\BlogsService\Domain\ContentBlock\Code;
use App\BlogsService\Domain\ContentBlock\Image;
use App\BlogsService\Domain\ContentBlock\Prose;
use App\BlogsService\Domain\ContentBlock\Social;
use App\Ds\Post\ContentBlock\ClipsBlock\ClipsBlockPresenter;
use App\Ds\Post\ContentBlock\CodeBlock\CodeBlockPresenter;
use App\Ds\Post\ContentBlock\ImageBlock\ImageBlockPresenter;
use App\Ds\Post\ContentBlock\ProseBlock\ProseBlockPresenter;
use App\Ds\Post\ContentBlock\SocialBlock\SocialBlockPresenter;
use App\Ds\Presenter;
use App\Exception\InvalidContentBlockException;
use App\ValueObject\CosmosInfo;

class AbstractPostPresenter extends Presenter
{
    /** @var CosmosInfo */
    protected $cosmosInfo;

    /** @var Presenter[] */
    protected $postPresenters;

    /** @return Presenter[] */
    public function getContentPresenters(): array
    {
        return $this->postPresenters;
    }

    protected function findPresenter(AbstractContentBlock $contentBlock, int $limit = null): Presenter
    {
        if ($contentBlock instanceof Prose) {
            return new ProseBlockPresenter($contentBlock, $limit);
        }

        if ($contentBlock instanceof Social) {
            return new SocialBlockPresenter($contentBlock);
        }

        if ($contentBlock instanceof Image) {
            return new ImageBlockPresenter($contentBlock);
        }

        if ($contentBlock instanceof Clips) {
            return new ClipsBlockPresenter($contentBlock, $this->cosmosInfo);
        }

        if ($contentBlock instanceof Code) {
            return new CodeBlockPresenter($contentBlock, $limit);
        }

        throw new InvalidContentBlockException('Could not display invalid Content Block');
    }
}
