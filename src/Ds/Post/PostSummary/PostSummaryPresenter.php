<?php
declare(strict_types = 1);

namespace App\Ds\Post\PostSummary;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\Post;
use App\Ds\InvalidOptionException;
use App\Ds\Presenter;

class PostSummaryPresenter extends Presenter
{
    protected $options = [
        'author_options' => [],
        'h_class' => 'gamma',
        'h_tag' => 2,
        'show_author' => true,
        'show_image' => true,
    ];

    /** @var Blog */
    private $blog;

    /** @var Post */
    private $post;

    public function __construct(Blog $blog, Post $post, array $options = [])
    {
        parent::__construct($options);

        $this->blog = $blog;
        $this->post = $post;
    }

    public function getBlogId(): string
    {
        return $this->blog->getId();
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    public function isFeaturedPost(): bool
    {
        $featuredPost = $this->blog->getFeaturedPost();

        return null !== $featuredPost && $featuredPost->getGuid() === $this->post->getGuid();
    }

    public function shouldShowAuthor(): bool
    {
        return $this->getOption('show_author') && $this->post->getAuthor();
    }

    public function shouldShowImage(): bool
    {
        return $this->getOption('show_image') && $this->post->getImage();
    }

    protected function validateOptions(array $options): void
    {
        parent::validateOptions($options);

        if (!is_array($options['author_options'])) {
            throw new InvalidOptionException("Option 'author_options' must be an aray");
        }

        if (!is_string($options['h_class'])) {
            throw new InvalidOptionException("Option 'h_class' must be a string");
        }

        if (!is_int($options['h_tag'])) {
            throw new InvalidOptionException("Option 'h_tag' must be an int");
        }

        if (!is_bool($options['show_author'])) {
            throw new InvalidOptionException("Option 'show_author' must be a boolean");
        }

        if (!is_bool($options['show_image'])) {
            throw new InvalidOptionException("Option 'show_image' must be a boolean");
        }
    }
}
