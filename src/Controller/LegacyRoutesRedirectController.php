<?php
declare(strict_types = 1);

namespace App\Controller;

use App\BlogsService\Domain\ValueObject\GUID;
use App\Controller\Helpers\Services\AtiAnalyticsHelper;
use App\Controller\Helpers\Services\BrandingHelper;
use App\Controller\Helpers\Services\PageMetadataHelper;
use Symfony\Component\HttpFoundation\Response;

class LegacyRoutesRedirectController extends BaseController
{

    public function __construct(
        PageMetadataHelper $pageMetadataHelper,
        BrandingHelper $brandingHelper,
        AtiAnalyticsHelper $atiAnalyticsHelper
    ) {
        parent::__construct($pageMetadataHelper, $brandingHelper, $atiAnalyticsHelper);

        // Cache these redirects for an hour
        $this->response()->setPublic()->setMaxAge(3600);
    }

    public function redirectPostShow(string $blogId, string $postName)
    {
        $guid = $this->generatePostGuid($blogId, $postName);
        return $this->cachedRedirectToRoute('post', ['blogId' => $blogId, 'guid' => $guid], Response::HTTP_MOVED_PERMANENTLY);
    }

    public function redirectAuthorShow(string $blogId, string $authorName)
    {
        $guid = $this->generateAuthorGuid($blogId, $authorName);
        return $this->cachedRedirectToRoute('author_show', ['blogId' => $blogId, 'guid' => $guid], Response::HTTP_MOVED_PERMANENTLY);
    }

    public function redirectTagShow(string $blogId, string $tagName)
    {
        $tagName = rtrim($tagName, '-');
        $tagName = str_replace('_', '-', strtolower($tagName));

        return $this->cachedRedirectToRoute('tag_show', ['blogId' => $blogId, 'tagId' => $tagName], Response::HTTP_MOVED_PERMANENTLY);
    }

    private function generatePostGuid(string $blogId, string $postName): GUID
    {
        $namespace = '/' . $blogId . '/posts/' . $postName;
        return $this->generateGuid($namespace);
    }

    private function generateAuthorGuid(string $blogId, string $authorName): GUID
    {
        $namespace = '/' . $blogId . '/authors/' . $authorName;
        return $this->generateGuid($namespace);
    }

    private function generateGuid(string $namespace): GUID
    {
        $hash = md5($namespace);

        return new GUID(sprintf(
            '%08s-%04s-%04x-%04x-%12s',
            substr($hash, 0, 8),
            substr($hash, 8, 4),
            (hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x3000,
            (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,
            substr($hash, 20, 12)
        ));
    }
}
