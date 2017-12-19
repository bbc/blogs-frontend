<?php
declare(strict_types=1);

namespace App\Service;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\Post;
use App\Translate\TranslateProvider;
use BBC\ProgrammesMorphLibrary\Entity\MorphView;
use BBC\ProgrammesMorphLibrary\Exception\MorphErrorException;
use BBC\ProgrammesMorphLibrary\MorphClient;
use Psr\Log\LoggerInterface;

class CommentsService
{
    /** @var string */
    private $apiKey;

    /** @var string */
    private $env;

    /** @var MorphClient */
    private $client;

    /** @var LoggerInterface */
    private $logger;

    /** @var TranslateProvider */
    private $translateProvider;

    public function __construct(
        LoggerInterface $logger,
        TranslateProvider $translateProvider,
        MorphClient $client,
        string $apiKey,
        string $env
    ) {
        $this->apiKey = $apiKey;
        $this->env = $env;
        $this->client = $client;
        $this->logger = $logger;
        $this->translateProvider = $translateProvider;
    }

    public function queuePostComments(Blog $blog, Post $post): void
    {
        $this->client->queueView(
            'bbc-morph-comments-view',
            [
                'apiKey' => $this->apiKey,
                'mode' => 'embedded',
                'idctaEnv' => $this->env,
                'forumId' => $this->getForumId($blog, $post),
            ],
            []
        );
    }

    public function getPostComments(Blog $blog, Post $post): ?MorphView
    {
        try {
            return $this->client->getView(
                'bbc-morph-comments-view',
                'comments-module',
                [
                    'apiKey' => $this->apiKey,
                    'mode' => 'embedded',
                    'idctaEnv' => $this->env,
                    'forumId' => $this->getForumId($blog, $post),
                ],
                []
            );
        } catch (MorphErrorException $e) {
            $this->logger->error($e->getMessage());
            return new MorphView(
                'comments-module',
                [],
                $this->translateProvider->getTranslate()->translate('error_comments'),
                []
            );
        }
    }

    private function getForumId(Blog $blog, Post $post): string
    {
        return 'blogs_' . $blog->getId() . $post->getForumId();
    }
}
