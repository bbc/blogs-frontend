<?php
declare(strict_types = 1);

namespace App\BlogsService\Repository;

use App\BlogsService\Query\IsiteQuery\GuidQuery;
use Psr\Http\Message\ResponseInterface;

class AuthorRepository extends AbstractRepository
{
    public function getAuthorByGUID(string $blogId, string $guid, $preview = false): ?ResponseInterface
    {
        $query = new GuidQuery();
        $query->setProject($blogId);
        $query->setContentId($guid);
        $query->setPreview($preview);

        return $this->getResponse($query);
    }
}
