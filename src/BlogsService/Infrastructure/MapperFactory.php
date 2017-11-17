<?php
declare(strict_types = 1);

namespace App\BlogsService\Infrastructure;

use App\BlogsService\Mapper\IsiteToDomain\AuthorMapper;
use App\BlogsService\Mapper\IsiteToDomain\BlogMapper;
use App\BlogsService\Mapper\IsiteToDomain\ContentBlockMapper;
use App\BlogsService\Mapper\IsiteToDomain\ModuleMapper;
use App\BlogsService\Mapper\IsiteToDomain\PostMapper;
use App\BlogsService\Mapper\IsiteToDomain\TagMapper;

class MapperFactory
{
    protected $instances = [];

    public function createPostMapper(): PostMapper
    {
        if (!isset($this->instances[PostMapper::class])) {
            $this->instances[PostMapper::class] = new PostMapper($this);
        }
        return $this->instances[PostMapper::class];
    }

    public function createBlogsmetadataMapper(): BlogMapper
    {
        if (!isset($this->instances[BlogMapper::class])) {
            $this->instances[BlogMapper::class] = new BlogMapper($this);
        }
        return $this->instances[BlogMapper::class];
    }

    public function createModuleMapper(): ModuleMapper
    {
//        return new ModuleMapper($this);
    }

    //TODO: CHECK THIS - THIS IS DNA, DONT THINK ITS NEEDED
//    public function createCommentForumMapper()
//    {
//        return new CommentForumMapper($this);
//    }
//
    public function createAuthorsMapper(): AuthorMapper
    {
//        return new AuthorMapper($this);
    }

    public function createTagMapper(): TagMapper
    {
//        return new TagMapper($this);
    }

    public function createContentBlockMapper(): ContentBlockMapper
    {
//        return new ContentBlockMapper($this);
    }
}
