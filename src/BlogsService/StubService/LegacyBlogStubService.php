<?php
declare(strict_types = 1);

namespace App\BlogsService\StubService;

use App\BlogsService\Service\LegacyBlogService;
use Symfony\Component\HttpFoundation\Response;

class LegacyBlogStubService extends LegacyBlogService
{
    public function __construct()
    {
    }

    public function getLegacyBlog(string $path): ?Response
    {
        return null;
    }
}
