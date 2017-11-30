<?php
declare(strict_types=1);

namespace Tests\App\BlogsService\Domain;

use App\BlogsService\Domain\Author;
use App\BlogsService\Domain\ContentBlock\Clips;
use App\BlogsService\Domain\Image;
use App\BlogsService\Domain\Post;
use App\BlogsService\Domain\ValueObject\FileID;
use App\BlogsService\Domain\ValueObject\GUID;
use App\BlogsService\Domain\ValueObject\Social;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class PostTest extends TestCase
{
    public function testConstructorSetsMembers()
    {
        $guid = new GUID('63a91e43-f154-4c89-9ccd-9cf10a332f90');

        $forumId = '_63a91e43_f154_4c89_9ccd_9cf10a332f90';
        $publishedDate = new DateTimeImmutable('2015-04-28T10:54:04.614+01:00');
        $title = 'Liam\'s leaving';
        $shortSynopsis = 'It’s been confirmed that James Forde will be waving goodbye to his role as Liam Butcher. Liam’s on-screen departure is set for later in the year, although we’re keeping details of how he’ll exit under wraps for now.';
        $author = new Author(
            new GUID('63a91e43-f154-4c89-9ccd-9cf10a332f90'),
            new FileID('blogs-author-1424161719'),
            'Qambar Raza',
            'Web Developer',
            'BBC Blogs',
            new Image('p02kzt0l'),
            new Social(
                '@QambarRaza',
                'https://www.facebook.com/qambarr',
                'https://plus.google.com/114400367936494835546/posts'
            )
        );

        $image = new Image('p02kzt0l');

        $id = 'p02ky7bx';
        $caption = 'Facilitators and participants discuss the Connected Studio: World Service Africa – Nairobi event';
        $playlistType = 'pid';
        $content = [new Clips($id, '', $caption, $playlistType)];

        $tags = [];
        $hasVideo = true;

        $testObj = new Post(
            $guid,
            $forumId,
            $publishedDate,
            $title,
            $shortSynopsis,
            $author,
            $image,
            $content,
            $tags
        );

        $this->assertSame($guid, $testObj->getGuid());
        $this->assertSame($publishedDate, $testObj->getPublishedDate());
        $this->assertEquals($title, $testObj->getTitle());
        $this->assertEquals($shortSynopsis, $testObj->getShortSynopsis());
        $this->assertSame($author, $testObj->getAuthor());
        $this->assertSame($image, $testObj->getImage());
        $this->assertSame($content, $testObj->getContent());
        $this->assertSame($tags, $testObj->getTags());
        $this->assertEquals($hasVideo, $testObj->hasVideo());
    }
}
