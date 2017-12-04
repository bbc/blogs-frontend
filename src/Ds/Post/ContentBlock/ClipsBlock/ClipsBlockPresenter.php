<?php
declare(strict_types = 1);

namespace App\Ds\Post\ContentBlock\ClipsBlock;

use App\BlogsService\Domain\ContentBlock\Clips;
use App\Ds\Presenter;
use App\ValueObject\CosmosInfo;

class ClipsBlockPresenter extends Presenter
{
    /** @var Clips */
    private $content;

    private $containerId;

    private static $COUNTER = 0;

    /** @var CosmosInfo */
    private $cosmosInfo;

    public function __construct(Clips $content, CosmosInfo $cosmosInfo, array $options = [])
    {
        parent::__construct($options);
        $this->content = $content;
        $this->cosmosInfo = $cosmosInfo;
    }

    public function getContainerId(): string
    {
        if (!isset($this->containerId)) {
            $this->containerId = 'smp-' . self::getClipsBlockCount();
        }

        return $this->containerId;
    }

    public function getCaption(): string
    {
        return $this->content->getCaption();
    }

    public function canRenderPlayer(): bool
    {
        return (bool) $this->content->getPlaylistType();
    }

    public function getPlayer(): string
    {
        $playlistType = $this->content->getPlaylistType();

        $player = null;

        if ($playlistType == 'pid') {
            $externalEmbedUrl = $this->cosmosInfo->getEndpointHost() . '/programmes/' . $this->content->getId() . '/player';

            $player = (object) [
                'container' => '#' . $this->getContainerId(),
                'pid' => $this->content->getId(),
                'playerSettings' => (object) [
                    'delayEmbed' => true,
                    'externalEmbedUrl' => $externalEmbedUrl,
                ],
            ];
        }

        if ($playlistType == 'xml') {
            $player = (object) [
                'container' => '#' . $this->getContainerId(),
                'xml' => $this->content->getUrl(),
                'externalEmbedUrl' => null,
            ];
        }
        return json_encode($player);
    }

    private static function getClipsBlockCount(): int
    {
        return self::$COUNTER++;
    }
}
