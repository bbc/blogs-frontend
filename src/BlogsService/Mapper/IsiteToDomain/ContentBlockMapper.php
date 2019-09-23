<?php
declare(strict_types = 1);

namespace App\BlogsService\Mapper\IsiteToDomain;

use App\BlogsService\Domain\ContentBlock\Clips;
use App\BlogsService\Domain\ContentBlock\Code;
use App\BlogsService\Domain\ContentBlock\CodePen;
use App\BlogsService\Domain\ContentBlock\Image;
use App\BlogsService\Domain\ContentBlock\Prose;
use App\BlogsService\Domain\ContentBlock\Social;
use SimpleXMLElement;

class ContentBlockMapper extends Mapper
{
    public function getDomainModel(SimpleXMLElement $isiteObject)
    {
        $type = $this->getType($isiteObject);
        if (!$type) {
            return null;
        }

        $form = $this->getForm($isiteObject);

        $contentBlock = null;

        switch ($type) {
            case 'prose':
                $contentBlockData = $form->content;
                $contentBlock = new Prose(
                    $this->getString($contentBlockData->prose)
                );
                break;
            case 'code':
                $contentBlockData = $form->content;
                $contentBlock = new Code(
                    $this->getString($contentBlockData->code)
                );
                break;
            case 'clips':
                $contentBlockData = $form->content;

                $url = $this->getString($contentBlockData->url);
                $id = $this->getString($contentBlockData->id);
                $playlistType = $this->getPlaylistType($id, $url);
                $caption = $this->getString($contentBlockData->caption);

                // if this doesn't exist, then the clip hasn't been defined in iSite properly
                if (\is_null($playlistType)) {
                    return null;
                }

                $contentBlock = new Clips(
                    $id,
                    $url,
                    $caption,
                    $playlistType
                );
                break;
            case 'image':
                $contentBlockData = $form->content;
                $contentBlock = new Image(
                    $this->getImageIfExists($contentBlockData->image),
                    $this->getString($contentBlockData->caption)
                );
                break;
            case 'social':
                $contentBlockData = $form->content;
                $contentBlock = new Social(
                    $this->getString($contentBlockData->link),
                    $this->getString($contentBlockData->alt)
                );
                break;
            case 'codepen':
                $contentBlockData = $form->content;
                $contentBlock = new CodePen(
                    'bGbBQaR', // codepen ID
                    'aaroniker' // author
                );
                break;
            default:
                $this->logger->error('Invalid post content block type. Found ' . $type);
                break;
        }

        return $contentBlock;
    }

    private function getType(SimpleXMLElement $isiteObject): ?string
    {
        $typeWithPrefix = $this->getMetaData($isiteObject)->type;
        if (!empty($typeWithPrefix)) {
            return str_replace('blogs-content-', '', $typeWithPrefix);
        }

        return null;
    }

    private function getPlaylistType(string $id, string $url): ?string
    {
        if (!empty($id)) {
            return 'pid';
        }

        if (!empty($url)) {
            return 'xml';
        }

        return null;
    }
}
