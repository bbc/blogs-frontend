<?php
declare(strict_types = 1);
namespace Tests\App\BlogsService\Domain\ValueObject;

use App\BlogsService\Domain\ValueObject\Social;
use PHPUnit\Framework\TestCase;

class SocialTest extends TestCase
{
    public function testConstructorSetsMembers()
    {
        $twitterHandle  = "@QambarRaza";
        $facebookUrl    = "https://www.facebook.com/qambarr";
        $googleUrl      = "https://plus.google.com/114400367936494835546/posts";

        $testObj = new Social(
            $twitterHandle,
            $facebookUrl,
            $googleUrl
        );

        $this->assertEquals($twitterHandle, $testObj->getTwitterUsername());
        $this->assertEquals($facebookUrl, $testObj->getFacebookUrl());
        $this->assertEquals($googleUrl, $testObj->getGooglePlusUrl());
    }
}
