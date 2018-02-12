<?php
declare(strict_types = 1);

namespace App\BlogsService\Mapper\IsiteToDomain;

use App\BlogsService\Domain\Post;
use App\BlogsService\Domain\ValueObject\GUID;
use Exception;
use SimpleXMLElement;

class PostMapper extends Mapper
{
    public function getDomainModel(SimpleXMLElement $isiteObject): ?Post
    {
        if (!isset($isiteObject->document)) {
            return null;
        }
        try {
            $formMetaData = $this->getFormMetaData($isiteObject);
            $form = $this->getForm($isiteObject);
            $guid = $this->getString($this->getMetaData($isiteObject)->guid);

            $forumId = str_replace('-', '_', '_' . $guid);

            $publishedDate = $this->getDateTime($formMetaData->{'published-date'});
            $title = $this->getString($formMetaData->{'title'});
            $shortSynopsis = $this->getString($formMetaData->{'short-synopsis'});

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
                $title,
                $shortSynopsis,
                $author,
                $image,
                $contentBlocks,
                $tags
            );
        } catch (Exception $e) {
            return null;
        }
    }
}
