<?php
declare(strict_types = 1);
namespace Tests\App\BlogsService\Domain;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\Image;
use App\BlogsService\Domain\ValueObject\GUID;
use App\BlogsService\Domain\ValueObject\FileID;
use PHPUnit\Framework\TestCase;

class BlogTest extends TestCase
{
    private $guid;
    private $fileId;
    private $id;
    private $name;
    private $shortSynopsis;
    private $description;
    private $showImageInDescription;
    private $language;
    private $istatsCountername;
    private $bbcSite;
    private $brandingId;
    private $modules;
    private $social;
    private $comments;
    private $featuredPost;
    private $image;
    private $isArchived;

    public function setUp()
    {
        $this->guid           = new GUID("63a91e43-f154-4c89-9ccd-9cf10a332f90");
        $this->fileId         = new FileID("blogs-Blog-1424161719");
        $this->id             = "blogs-internet";
        $this->name           = "Internet";
        $this->shortSynopsis  = "This is internet blog";
        $this->description    = "The best blog ever and ever!!";
        $this->image          = new Image("p02kzt0l");
        $this->showImageInDescription = true;
        $this->description    = "BBC Blogs";
        $this->modules        = array();
        $this->istatsCountername = 'eastenders';
        $this->social         = null;
        $this->comments       = null;
        $this->bbcSite        = 'kl-bitesize';
        $this->brandingId     = 'internetblog';
        $this->featuredPost   = null; /** TODO ADD A FEATURED POST WHEN POSTS IMPLEMENTED */
        $this->language       = "en-GB";
        $this->isArchived     = false;
    }

    public function testConstructorSetsMembers()
    {
        $testObj = $this->constructBlog();
        $this->assertSame($this->guid, $testObj->getGuid());
        $this->assertSame($this->fileId, $testObj->getFileId());
        $this->assertEquals($this->id, $testObj->getId());
        $this->assertEquals($this->name, $testObj->getName());
        $this->assertEquals($this->shortSynopsis, $testObj->getShortSynopsis());
        $this->assertEquals($this->description, $testObj->getDescription());
        $this->assertSame($this->image, $testObj->getImage());
        $this->assertEquals($this->showImageInDescription, $testObj->getShowImageInDescription());
        $this->assertEquals($this->language, $testObj->getLanguage());
        $this->assertEquals($this->istatsCountername, $testObj->getIstatsCountername());
        $this->assertSame($this->social, $testObj->getSocial());
        $this->assertSame($this->comments, $testObj->getComments());
        $this->assertEquals($this->bbcSite, $testObj->getBbcSite());
        $this->assertEquals($this->brandingId, $testObj->getBrandingId());
        $this->assertSame($this->modules, $testObj->getModules());
        $this->assertSame($this->isArchived, $testObj->getIsArchived());
        $this->assertSame($this->featuredPost, $testObj->getFeaturedPost());
    }

    private function constructBlog(): Blog
    {
        return new Blog(
            $this->guid,
            $this->fileId,
            $this->id,
            $this->name,
            $this->shortSynopsis,
            $this->description,
            $this->showImageInDescription,
            $this->language,
            $this->istatsCountername,
            $this->bbcSite,
            $this->brandingId,
            $this->modules,
            $this->social,
            $this->comments,
            $this->featuredPost,
            $this->image,
            $this->isArchived
        );
    }
}
