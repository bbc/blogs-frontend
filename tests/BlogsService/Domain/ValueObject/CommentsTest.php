<?php
declare(strict_types = 1);

namespace Tests\App\BlogsService\Domain\ValueObject;

use App\BlogsService\Domain\ValueObject\Comments;
use PHPUnit\Framework\TestCase;

class CommentsTest extends TestCase
{
    public function testCommentsEnabled()
    {
        $comments = new Comments('anything');
        $this->assertTrue($comments->isEnabled());
    }

    public function testCommentsNotEnabled()
    {
        $comments = new Comments('');
        $this->assertFalse($comments->isEnabled());
    }
}
