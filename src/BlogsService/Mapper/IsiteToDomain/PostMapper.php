<?php
declare(strict_types = 1);

namespace App\BlogsService\Mapper\IsiteToDomain;

use App\BlogsService\Domain\Author;
use App\BlogsService\Domain\Post;
use App\BlogsService\Domain\Tag;
use App\BlogsService\Domain\ValueObject\GUID;
use App\BlogsService\Domain\ValueObject\FileID;
use App\BlogsService\Domain\IsiteEntity;
use SimpleXMLElement;

class PostMapper extends Mapper
{
    public function getDomainModel(SimpleXMLElement $isiteObject): ?IsiteEntity
    {
        if (!isset($isiteObject->document)) {
            return null;
        }
        try {
            $formMetaData = $this->getFormMetaData($isiteObject);
            $form = $this->getForm($isiteObject);
            $guid = $this->getString($this->getMetaData($isiteObject)->guid);

            //Note this forumid doesn't have a prefix 'blogs_' . $blog->short_project_id .
            //which is why we get the blog object in domain to add that prefix.
            $forumId = str_replace('-', '_', '_' . $guid);

            $publishedDate = $this->getDate($formMetaData->{'published-date'});
            $title = $this->getString($formMetaData->{'title'});
            $shortSynopsis = $this->getString($formMetaData->{'short-synopsis'});

            $author = null;

            $authorMetadata = $formMetaData
                ->{'author'}
                ->result
                ->metadata;
            if (is_object($authorMetadata)) {
                $authorGUID = (string) $authorMetadata->guid;

                if (!empty($authorGUID)) {
                    $author = $this->mapperFactory->createAuthorsMapper()->getDomainModel(
                        $formMetaData->{'author'}->result
                    );
                }
            }

            $image = $this->getImageIfExists($formMetaData->{'post-image'});

            $contentBlocks = array();
            $contentBlockContent = $form->content->xpath("./*");

            foreach ($contentBlockContent as $contentBlock) {
                $result = $contentBlock->{'blog-post-content'}->result;

                if (is_object($result->metadata)) {
                    $contentBlocks[] = $this->mapperFactory
                        ->createContentBlockMapper()->getDomainModel(
                            $result
                        );
                }
            }
            $tags = array();
            $tagContent = $form->{'Tags'}->{'tag-content'};

            foreach ($tagContent as $tag) {
                if (!isset($tag->{'tag'}->result->document)) {
                    continue;
                }
                $tagMetadata = $tag->{'tag'}->result->metadata;
                $formMetaData = $this->getFormMetaData($tag->{'tag'}->result);

                if (is_object($formMetaData) && !is_null($tagMetadata)) {
                    $tags[] = $this->mapperFactory->createTagMapper()->getDomainModel(
                        $tag->{'tag'}->result
                    );
                }
            }

            return new Post(
                new GUID($guid),
                new FileID($this->getString($this->getMetaData($isiteObject)->fileId)),
                $forumId,
                $publishedDate,
                $title,
                $shortSynopsis,
                $author,
                $image,
                $contentBlocks,
                $tags
            );
        } catch (\Exception $e) {
            return null;
        }
    }
}
