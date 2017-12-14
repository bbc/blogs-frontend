<?php
declare(strict_types = 1);

namespace Tests\App\Builders;

use App\BlogsService\Domain\Author;
use App\BlogsService\Domain\ContentBlock\Prose;
use App\BlogsService\Domain\Image;
use App\BlogsService\Domain\Post;
use App\BlogsService\Domain\Tag;
use App\BlogsService\Domain\ValueObject\FileID;
use App\BlogsService\Domain\ValueObject\GUID;
use App\BlogsService\Domain\ValueObject\Social;
use Cake\Chronos\Chronos;

class PostBuilder implements BuilderInterface
{
    /** @var GUID */
    private $guid;

    /** @var string */
    private $forumId;

    /** @var Chronos */
    private $publishedDate;

    /** @var string */
    private $title;

    /** @var string */
    private $shortSynopsis;

    /** @var Author */
    private $author;

    /** @var Image */
    private $image;

    /** @var array */
    private $content;

    /** @var array */
    private $tags;

    public function build()
    {
        return new Post(
            $this->guid,
            $this->forumId,
            $this->publishedDate,
            $this->title,
            $this->shortSynopsis,
            $this->author,
            $this->image,
            $this->content,
            $this->tags
        );
    }

    public function withPublishedDate(Chronos $date)
    {
        $this->publishedDate = $date;
        return $this;
    }

    public function withTitle(string $title)
    {
        $this->title = $title;
        return $this;
    }

    public function withShortSynopsis(string $shortSynopsis)
    {
        $this->shortSynopsis = $shortSynopsis;
        return $this;
    }

    public function withAuthor(Author $author)
    {
        $this->author = $author;
        return $this;
    }

    public function withImage(Image $image)
    {
        $this->image = $image;
        return $this;
    }

    public function withContent(array $content)
    {
        $this->content = $content;
        return $this;
    }

    public function withTags(array $tags)
    {
        $this->tags = $tags;
        return $this;
    }

    public function withForumId(string $forumId)
    {
        $this->forumId = $forumId;
        return $this;
    }

    public function withGuid(GUID $guid)
    {
        $this->guid = $guid;
        return $this;
    }

    public static function default()
    {
        return new self();
    }

    public function __construct()
    {
        $this->guid = new GUID('72cd7d2e-ff79-4f1f-9ae0-1118c5aa727a');
        $this->forumId = 'forumid';
        $this->publishedDate = Chronos::create(2017, 5, 9, 12, 20);
        $this->title = 'Post Title';
        $this->shortSynopsis = 'This is the post short synopsis';
        $this->author = new Author(
            new GUID('72cd7d2e-ff79-4f1f-9ae0-1118c5aa727b'),
            new FileID('thisisafileid'),
            'Author Name',
            'Author Role',
            'Author Description',
            new Image('p017j1r1'),
            new Social('', '', '')
        );
        $this->image = new Image('p017j1r1');
        $this->content = [new Prose('This is a prose block in a post. It is a string of text.')];
        $this->tags = [new Tag(new FileID('tagfileid'), 'sometag')];
    }
}
