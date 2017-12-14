<?php
declare(strict_types=1);

namespace App\Service;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\Post;
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

    public function __construct(LoggerInterface $logger, MorphClient $client, string $apiKey, string $env)
    {
        $this->apiKey = $apiKey;
        $this->env = $env;
        $this->client = $client;
        $this->logger = $logger;
    }

    public function queuePostComments(Blog $blog, Post $post): void
    {
        $this->client->queueView(
            'bbc-morph-comments-view',
            [
                'apiKey' => $this->apiKey,
                'mode' => 'embedded',
                'idctaEnv' => $this->env,
                'forumId' => 'blogs_' . $blog->getId() . $post->getForumId(),
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
                    'forumId' => 'blogs_' . $blog->getId() . $post->getForumId(),
                ],
                [],
                'There was an error when fetching the comments. Please try again in a few moments.'
            );
        }
        catch (MorphErrorException $e) {
            $this->logger->error($e->getMessage());
            return new MorphView(
                'comments-module',
                [],
                'There was an error when fetching the comments. Please try again in a few moments.',
                []
            );
        }
    }
}
