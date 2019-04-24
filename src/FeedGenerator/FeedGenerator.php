<?php
declare(strict_types = 1);

namespace App\FeedGenerator;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\Post;
use App\Ds\PresenterFactory;
use App\Helper\ApplicationTimeProvider;
use App\Translate\TranslatableTrait;
use App\Translate\TranslateProvider;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig_Environment;
use Zend\Feed\Writer\Entry;
use Zend\Feed\Writer\Feed;

class FeedGenerator
{
    use TranslatableTrait;

    /** @var UrlGeneratorInterface */
    private $router;

    /** @var Twig_Environment */
    private $twig;

    /** @var PresenterFactory */
    private $presenterFactory;

    public function __construct(
        TranslateProvider $translateProvider,
        UrlGeneratorInterface $router,
        Twig_Environment $twig,
        PresenterFactory $presenterFactory
    ) {
        $this->translateProvider = $translateProvider;
        $this->router = $router;
        $this->twig = $twig;
        $this->presenterFactory = $presenterFactory;
    }

    public function generateAtomFeed(Blog $blog, array $posts): string
    {
        return $this->generateFeed($blog, $posts, 'atom');
    }

    public function generateRssFeed(Blog $blog, array $posts): string
    {
        return $this->generateFeed($blog, $posts, 'rss');
    }

    private function generateFeed(Blog $blog, array $posts, string $type): string
    {
        $feed = new Feed();

        $blogUrl = $this->router->generate('blog', ['blogId' => $blog->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $feedUrl = $this->router->generate('feed_' . $type, ['blogId' => $blog->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        $lastModified = count($posts) > 0 ? $posts[0]->getPublishedDate() : ApplicationTimeProvider::getTime();
        [$lang, $country] = explode('-', $blog->getLanguage());

        $feed->setTitle($blog->getName() . ' ' . $this->tr('feed'));
        $feed->setLink($blogUrl);
        $feed->setFeedLink($feedUrl, $type);
        $feed->setDescription($blog->getDescription());
        $feed->setDateModified($lastModified->getTimestamp());
        $feed->setLanguage($lang);
        $feed->setEncoding('UTF-8');

        foreach ($posts as $post) {
            $feedItem = $this->addFeedEntry($feed->createEntry(), $blog, $post, $type);
            $feed->addEntry($feedItem);
        }

        $output = $feed->export($type);

        // Yay a nasty hack so we can display html in atom feeds
        if ($type == 'atom') {
            $output = preg_replace('!<content .*?>\s*<xhtml:div .*?>(.*?)</xhtml:div>!is', '<content type="html">$1', $output);
        }

        return $output;
    }

    private function addFeedEntry(Entry $feedItem, Blog $blog, Post $post, string $type)
    {
        $postUrl = $this->router->generate(
            'post',
            ['blogId' => $blog->getId(), 'guid' => $post->getGuid()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $feedItem->setTitle($post->getTitle());
        $feedItem->setLink($postUrl);
        $feedItem->setDescription($post->getShortSynopsis());
        $feedItem->setDateModified($post->getPublishedDate()->getTimestamp());
        $feedItem->setDateCreated($post->getPublishedDate()->getTimestamp());

        if ($post->getAuthor()) {
            $feedItem->addAuthor(['name' => $post->getAuthor()->getName()]);
        }

        $postContent = $this->generatePostFeedContent($post);

        if ($type == 'atom') {
            $postContent = html_entity_decode($postContent, ENT_QUOTES, 'UTF-8');
            $postContent = htmlspecialchars($postContent, ENT_QUOTES, 'UTF-8');
        }

        if ($postContent) {
            $feedItem->setContent($postContent);
        }

        return $feedItem;
    }

    private function generatePostFeedContent(Post $post): string
    {
        $contentBlocks = $post->getContent();

        if (!$contentBlocks) {
            return '';
        }

        $postPresenter = $this->presenterFactory->postFullPresenter($contentBlocks);
        $content = $this->twig->render(
            $postPresenter->getTemplatePath(),
            [$postPresenter->getTemplateVariableName() => $postPresenter]
        );

        return $content;
    }
}
