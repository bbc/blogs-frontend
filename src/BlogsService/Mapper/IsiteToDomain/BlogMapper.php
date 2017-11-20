<?php
declare(strict_types=1);

namespace App\BlogsService\Mapper\IsiteToDomain;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\IsiteEntity;
use App\BlogsService\Domain\ValueObject\Comments;
use App\BlogsService\Domain\ValueObject\FileID;
use App\BlogsService\Domain\ValueObject\GUID;
use App\BlogsService\Domain\ValueObject\Social;
use SimpleXMLElement;

class BlogMapper extends Mapper
{
    const BLOG_PREFIX = 'blogs-';

    public function getDomainModel(SimpleXMLElement $isiteObject): ?Blog
    {
        $formMetaData = $this->getFormMetaData($isiteObject);
        $form = $this->getForm($isiteObject);
        $id = $this->_getProjectId($form);

        $name = $this->getString($formMetaData->{'blog-name'});
        $shortSynopsis = $this->getString($formMetaData->{'short-synopsis'});
        $description = $this->getString($formMetaData->description);
        $image = $this->getImage($formMetaData->{'blog-image'});
        $showImageInDescription = $this->getBoolean($formMetaData->{'show-image-in-description'});
        $language = $this->getString($formMetaData->language);
        $istatsCountername = $this->getString($formMetaData->{'istats-countername'});

        $social = null;
        $twitterUsername = $this->getString($formMetaData->{'twitter-username'});
        $facebookUrl = $this->getString($formMetaData->{'facebook-url'});
        $googlePlusUrl = $this->getString($formMetaData->{'google-plus-url'});

        if (!empty($twitterUsername) || !empty($facebookUrl) || !empty($googlePlusUrl)) {
            $social = new Social($twitterUsername, $facebookUrl, $googlePlusUrl);
        }

        // TODO check when integrating new comments module
        $commentsSiteId = $this->getString($formMetaData->{'site-id-comments'});
        $comments = new Comments($commentsSiteId);

        $bbcSite = $this->getString($formMetaData->{'bbc-site'});
        $brandingId = $this->getString($formMetaData->{'blogs-branding-id'});

        $featuredPost = null;

        // Check is there is a featured post
        if (!empty($form->{'section-9'}->featured)) {
            $postMetadata = $form
                ->{'section-9'}
                ->{'featured'}
                ->result
                ->metadata;
            if (!is_null($postMetadata)) {
                $featuredPost = $this->mapperFactory->createPostMapper()->getDomainModel(
                    $form->{'section-9'}->featured->result
                );
            }
        }

        $modules = [];
        //check if module is in the data
        if (!empty($form->{'sidebar-modules'}->{'grid-12'})) {
            $moduleContent = $form->{'sidebar-modules'}->{'grid-12'};

            foreach ($moduleContent as $module) {
                $moduleMetadata = $module->{'module'}->result->metadata;
                if (!is_null($moduleMetadata)) {
                    $modules[] = $this->mapperFactory->createModuleMapper()->getDomainModel(
                        $module->{'module'}->result
                    );
                }
            }
        }

        $isArchived = $this->getBoolean($form->{'section-27'}->{'is-archived'});

        return new Blog(
            $id,
            $name,
            $shortSynopsis,
            $description,
            $showImageInDescription,
            $language,
            $istatsCountername,
            $bbcSite,
            $brandingId,
            $modules,
            $social,
            $comments,
            $featuredPost,
            $image,
            $isArchived
        );
    }

    private function _getProjectId(SimpleXMLElement $form): ?string
    {
        if (!method_exists($form, 'getNamespaces')) {
            return null;
        }

        $namespaces = $form->getNamespaces();
        $projectNameSpace = reset($namespaces);
        $projectNameSpaceParts = explode('/', $projectNameSpace);
        $id = $projectNameSpaceParts[count($projectNameSpaceParts) - 2];

        return $id;
    }
}
