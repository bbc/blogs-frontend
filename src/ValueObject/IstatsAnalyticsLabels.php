<?php
declare(strict_types = 1);

namespace App\ValueObject;

use App\BlogsService\Domain\Blog;

class IstatsAnalyticsLabels
{
    /** @var string[] */
    private $labels = [
        'app_name' => 'blogs5',
        'prod_name' => 'blogs',
        'has_comments' => 'false',
        'page_number' => '1',
    ];

    /** @var string[][] */
    private $orbLabels = [];

    /**
     * @param Blog|null $blog
     * @param string $pageType
     * @param string $appVersion
     * @param bool $hasVideo
     * @param string[] $otherLabels
     */
    public function __construct(?Blog $blog, string $pageType, string $appVersion, bool $hasVideo, array $otherLabels)
    {
        if ($blog !== null) {
            $this->labels['bbc_site'] = $blog->getBbcSite();
            $this->labels['has_comments'] = ($blog->getComments() !== null && $blog->getComments()->isEnabled()) ? 'true' : 'false';
            $this->labels['blog_title'] = $blog->getName();
            $this->labels['blog_project_id'] = $blog->getId();
            $this->labels['blog_language'] = $blog->getLanguage();
        }

        $this->labels['app_version'] = $appVersion;
        $this->labels['has_emp'] = $hasVideo ? 'true' : 'false';
        $this->labels = array_merge($this->labels, $otherLabels);
        $this->labels['blogs_page_type'] = $pageType;
        if ($this->getPageType($pageType)) {
            $this->labels['page_type'] = $this->getPageType($pageType);
        }

        $this->labels['accept_language'] = $this->getAcceptLanguage();
    }

    public function orbLabels(): array
    {
        // The ORB Mustache template wants the labels in a slightly clunky format. We should oblige it.
        if (empty($this->orbLabels)) {
            foreach ($this->labels as $key => $value) {
                $this->orbLabels[] = ['key' => $key, 'value' => urlencode($value)];
            }
        }

        return $this->orbLabels;
    }

    private function getPageType(string $index): ?string
    {
        $pageNames = [];
        $pageNames['index_index'] = 'main index for all blogs';
        $pageNames['index_single'] = 'index';
        $pageNames['post_show'] = 'individual post';
        $pageNames['author_index'] = 'index all authors';
        $pageNames['author_show'] = 'index per author';
        $pageNames['tag_show'] = 'index per tag';
        $pageNames['tag_index'] = 'index all tags';
        $pageNames['post_index'] = 'posts';
        $pageNames['post_date'] = 'posts by date';
        $pageNames['author_indexatoz'] = 'index all authors by a-z';
        $pageNames['author_letter'] = 'index all authors by letter';
        $pageNames['api_request'] = 'Blogs API';

        if (!array_key_exists($index, $pageNames)) {
            return null;
        }

        return $pageNames[$index];
    }

    private function getAcceptLanguage(): string
    {
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 50);
            return preg_replace('/[^a-zA-Z\d\s\-\,\;\=\.\*:]/i', '', $lang);
        }

        return '';
    }
}
