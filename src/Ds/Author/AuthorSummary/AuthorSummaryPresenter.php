<?php
declare(strict_types = 1);

namespace App\Ds\Author\AuthorSummary;

use App\BlogsService\Domain\Author;
use App\Ds\InvalidOptionException;
use App\Ds\Presenter;

class AuthorSummaryPresenter extends Presenter
{
    protected $options = [
        'h_tag' => 2,
    ];

    /** @var Author */
    private $author;

    /** @var string */
    private $blogId;

    /** @var int */
    private $postCount;

    public function __construct(Author $author, string $blogId, int $postCount, array $options = [])
    {
        parent::__construct($options);

        $this->author = $author;
        $this->blogId = $blogId;
        $this->postCount = $postCount;
    }

    public function getAuthor(): Author
    {
        return $this->author;
    }

    public function getBlogId(): string
    {
        return $this->blogId;
    }
    public function getPostCount(): int
    {
        return $this->postCount;
    }

    protected function validateOptions(array $options): void
    {
        parent::validateOptions($options);

        if (!\is_int($options['h_tag'])) {
            throw new InvalidOptionException("Option 'h_tag' must be an int");
        }
    }
}
