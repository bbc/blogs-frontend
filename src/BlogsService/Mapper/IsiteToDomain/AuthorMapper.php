<?php
declare(strict_types = 1);

namespace App\BlogsService\Mapper\IsiteToDomain;

use App\BlogsService\Domain\Author;
use App\BlogsService\Domain\ValueObject\FileID;
use App\BlogsService\Domain\ValueObject\GUID;
use App\BlogsService\Domain\ValueObject\Social;
use Exception;
use SimpleXMLElement;

class AuthorMapper extends Mapper
{
    public function getDomainModel(SimpleXMLElement $isiteObject): ?Author
    {
        $formMetaData = $this->getFormMetaData($isiteObject);

        try {
            $name = $this->getString($formMetaData->name);

            $twitterUsername = $this->getString($formMetaData->{'twitter-username'});
            if ($twitterUsername == '@') {
                $twitterUsername = null;
            }

            $socialInfo = new Social(
                $twitterUsername ?? '',
                $this->getString($formMetaData->{'facebook-url'}) ?? '',
                $this->getString($formMetaData->{'google-plus-url'} ?? '')
            );

            $role = $this->getString($formMetaData->role);

            $description = $this->getString($formMetaData->description);

            $image = $this->getImage($formMetaData->picture);

            return new Author(
                new GUID($this->getString($this->getMetaData($isiteObject)->guid)),
                new FileID($this->getString($this->getMetaData($isiteObject)->fileId)),
                $name,
                $role,
                $description,
                $image,
                $socialInfo
            );
        } catch (Exception $e) {
            return null;
        }
    }
}
