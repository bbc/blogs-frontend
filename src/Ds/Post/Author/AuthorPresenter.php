<?php
declare(strict_types = 1);

namespace App\Ds\Post\Author;

use App\BlogsService\Domain\Author;
use App\BlogsService\Domain\Image;
use App\Ds\Presenter;

class AuthorPresenter extends Presenter
{
    /** @var Author */
    private $author;

    /** @var string */
    private $blogId;

    protected $options = [
        'is_slimline' => false,
    ];

    public function __construct(Author $author, string $blogId, array $options = [])
    {
        parent::__construct($options);

        $this->author = $author;
        $this->blogId = $blogId;
    }

    public function getBlogId(): string
    {
        return $this->blogId;
    }

    public function getGuid(): string
    {
        return (string) $this->author->getGuid();
    }

    public function getName(): string
    {
        return $this->author->getName();
    }

    public function getRole(): string
    {
        return $this->author->getRole();
    }

    public function getImage(): ?Image
    {
        if ($this->getOption('is_slimline')) {
            return null;
        }

        return $this->author->getImage();
    }

    protected function validateOptions(array $options): void
    {
        parent::validateOptions($options);

        if (!is_bool($options['is_slimline'])) {
            throw new InvalidOptionException("Option 'is_lazy_loaded' must be a boolean");
        }
    }
}
