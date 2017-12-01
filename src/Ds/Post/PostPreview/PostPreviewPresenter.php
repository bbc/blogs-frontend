<?php
declare(strict_types = 1);

namespace App\Ds\Post\PostPreview;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\ContentBlock\AbstractContentBlock;
use App\BlogsService\Domain\ContentBlock\Clips;
use App\BlogsService\Domain\ContentBlock\Code;
use App\BlogsService\Domain\ContentBlock\Image;
use App\BlogsService\Domain\ContentBlock\Prose;
use App\BlogsService\Domain\ContentBlock\Social;
use App\BlogsService\Domain\Post;
use App\Ds\ContentBlock\ClipsBlock\ClipsBlockPresenter;
use App\Ds\ContentBlock\CodeBlock\CodeBlockPresenter;
use App\Ds\ContentBlock\ImageBlock\ImageBlockPresenter;
use App\Ds\ContentBlock\ProseBlock\ProseBlockPresenter;
use App\Ds\ContentBlock\SocialBlock\SocialBlockPresenter;
use App\Ds\Presenter;
use App\Exception\InvalidContentBlockException;
use App\ValueObject\CosmosInfo;

class PostPreviewPresenter extends Presenter
{
    /** @var Blog */
    private $blog;

    /** @var int */
    private $charLimit;

    /** @var CosmosInfo */
    private $cosmosInfo;

    /** @var Post */
    private $post;

    /** @var bool */
    private $showReadMore = false;

    public function __construct(CosmosInfo $cosmosInfo, Blog $blog, Post $post, int $charLimit, array $options = [])
    {
        parent::__construct($options);

        $this->blog = $blog;
        $this->post = $post;
        $this->charLimit = $charLimit;
        $this->cosmosInfo = $cosmosInfo;
    }

    public function getBlogId(): string
    {
        return $this->blog->getId();
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    /** Presenter[] */
    public function getContentPresenters(): array
    {
        $limit = $this->charLimit;
        $presenters = [];
        foreach($this->post->getContent() as $contentBlock) {
            $presenters[] = $this->findPresenter($contentBlock, $limit);
            $limit -= $contentBlock->getCharacterCount();
            if ($limit <= 0) {
                $this->showReadMore = true;
                return $presenters;
            }
        }

        return $presenters;
    }

    public function shouldShowShowMoreLink(): bool
    {
        return $this->showReadMore;
    }

    private function findPresenter(AbstractContentBlock $contentBlock, int $limit): Presenter
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
