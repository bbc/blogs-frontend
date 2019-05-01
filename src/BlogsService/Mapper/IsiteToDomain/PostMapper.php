<?php
declare(strict_types = 1);

namespace App\BlogsService\Mapper\IsiteToDomain;

use App\BlogsService\Domain\Post;
use App\BlogsService\Domain\ValueObject\GUID;
use App\Exception\PostMappingException;
use App\Exception\WrongEntityTypeException;
use SimpleXMLElement;

class PostMapper extends Mapper
{
    public function getDomainModel(SimpleXMLElement $isiteObject): ?Post
    {
        if (!isset($isiteObject->document)) {
            return null;
        }

        if (!isset($isiteObject->metadata->type) || (string) $isiteObject->metadata->type !== 'blogs-post') {
            throw new WrongEntityTypeException("Invalid type passed into PostMapper");
        }

        $formMetaData = $this->getFormMetaData($isiteObject);
        $form = $this->getForm($isiteObject);
        $guid = $this->getString($this->getMetaData($isiteObject)->guid);

        $forumId = str_replace('-', '_', '_' . $guid);

        if (isset($formMetaData->{'published-date'}) && isset($formMetaData->{'title'}) && isset($formMetaData->{'short-synopsis'})) {
            $publishedDate = $this->getDateTime($formMetaData->{'published-date'});
            $displayDate = $this->getDisplayPublishedDateTime($formMetaData->{'published-date'});
            $title = $this->getString($formMetaData->{'title'});
            $shortSynopsis = $this->getString($formMetaData->{'short-synopsis'});
        } else {
            throw new PostMappingException('Could not map post');
        }

        $author = null;
        $authorMetadata = $formMetaData
            ->{'author'}
            ->result
            ->metadata;
        if (\is_object($authorMetadata)) {
            $authorGUID = (string) $authorMetadata->{'guid'};

            if (!empty($authorGUID)) {
                $author = $this->mapperFactory->createAuthorsMapper()->getDomainModel(
                    $formMetaData->{'author'}->result
                );
            }
        }

        $image = $this->getImageIfExists($formMetaData->{'post-image'});

        $contentBlocks = [];
        $contentBlockContent = $form->content->xpath("./*");

        $contentBlockMapper = $this->mapperFactory->createContentBlockMapper();

        foreach ($contentBlockContent as $contentBlock) {
            $result = $contentBlock->{'blog-post-content'}->result;

            if (\is_object($result->metadata) && $contentBlockMapper->getDomainModel($result)) {
                $contentBlocks[] = $contentBlockMapper->getDomainModel($result);
            }
        }
        $tags = [];
        $tagContent = $form->{'Tags'}->{'tag-content'};

        foreach ($tagContent as $tag) {
            if (isset($tag->{'tag'}->result->document)) {
                $tagMetadata = $tag->{'tag'}->result->metadata;
                $formMetaData = $this->getFormMetaData($tag->{'tag'}->result);

                if (\is_object($formMetaData) && !\is_null($tagMetadata)) {
                    $tags[] = $this->mapperFactory->createTagMapper()->getDomainModel(
                        $tag->{'tag'}->result
                    );
                }
            }
        }

        return new Post(
            new GUID($guid),
            $forumId,
            $publishedDate,
            $displayDate,
            $title,
            $shortSynopsis,
            $author,
            $image,
            $contentBlocks,
            $tags
        );
    }
}
