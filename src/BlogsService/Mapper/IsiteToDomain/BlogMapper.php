<?php
declare(strict_types=1);

namespace App\BlogsService\Mapper\IsiteToDomain;

use App\BlogsService\Domain\Blog;
use App\BlogsService\Domain\ValueObject\Comments;
use App\BlogsService\Domain\ValueObject\Social;
use App\Exception\PostMappingException;
use App\Exception\WrongEntityTypeException;
use SimpleXMLElement;

class BlogMapper extends Mapper
{
    private const BLOG_PREFIX = 'blogs-';

    public function getDomainModel(SimpleXMLElement $isiteObject): ?Blog
    {
        $formMetaData = $this->getFormMetaData($isiteObject);
        $form = $this->getForm($isiteObject);
        $projectId = $this->_getProjectId($form);

        $name = $this->getString($formMetaData->{'blog-name'});
        $shortSynopsis = $this->getString($formMetaData->{'short-synopsis'});
        $description = $this->getString($formMetaData->description);
        $image = $this->getImage($formMetaData->{'blog-image'});
        $showImageInDescription = $this->getBoolean($formMetaData->{'show-image-in-description'});
        $language = $this->getString($formMetaData->language);
        $istatsCountername = $this->getString($formMetaData->{'istats-countername'});

        $twitterUsername = $this->getString($formMetaData->{'twitter-username'}) ?? '';
        if ($twitterUsername == '@') {
            $twitterUsername = '';
        }

        $facebookUrl = $this->getString($formMetaData->{'facebook-url'}) ?? '';
        $googlePlusUrl = $this->getString($formMetaData->{'google-plus-url'}) ?? '';

        $social = new Social($twitterUsername, $facebookUrl, $googlePlusUrl);

        $hasCommentsEnabled = ($this->getString($formMetaData->{'site-id-comments'}) ?? '') !== '';

        $bbcSite = $this->getString($formMetaData->{'bbc-site'}) ?? '';
        $brandingId = $this->getString($formMetaData->{'blogs-branding-id'});

        $featuredPost = null;

        // Check is there is a featured post
        if (!empty($form->{'section-9'}->featured)) {
            try {
                $postMetadata = $form
                    ->{'section-9'}
                    ->{'featured'}
                    ->result
                    ->metadata;
                if (!\is_null($postMetadata)) {
                    $featuredPost = $this->mapperFactory->createPostMapper()->getDomainModel(
                        $form->{'section-9'}->featured->result
                    );
                }
            } catch (PostMappingException | WrongEntityTypeException $e) {
                // We're not doing anything here because in reality, this will only occur if a featured post
                // is subsequently unpublished, in which case we shouldn't display it or break the page
            }
        }

        $modules = [];
        //check if module is in the data
        if (!empty($form->{'sidebar-modules'}->{'grid-12'})) {
            $moduleContent = $form->{'sidebar-modules'}->{'grid-12'};

            foreach ($moduleContent as $module) {
                $moduleMetadata = $module->{'module'}->result->metadata;
                if (!\is_null($moduleMetadata)) {
                    $modules[] = $this->mapperFactory->createModuleMapper()->getDomainModel(
                        $module->{'module'}->result
                    );
                }
            }
        }

        $isArchived = $this->getBoolean($form->{'section-27'}->{'is-archived'});

        return new Blog(
            str_replace(self::BLOG_PREFIX, '', $projectId),
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
            $hasCommentsEnabled,
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
        $id = $projectNameSpaceParts[\count($projectNameSpaceParts) - 2];

        return $id;
    }
}
