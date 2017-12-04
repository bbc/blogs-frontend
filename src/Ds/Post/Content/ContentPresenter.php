<?php
declare(strict_types = 1);

namespace App\Ds\Post\Content;

use App\Ds\Post\AbstractPostPresenter;
use App\ValueObject\CosmosInfo;

class ContentPresenter extends AbstractPostPresenter
{
    public function __construct(array $contentBlocks, CosmosInfo $cosmosInfo, array $options = [])
    {
        parent::__construct($options);
        $this->cosmosInfo = $cosmosInfo;
        $this->postPresenters = array_map([$this, 'findPresenter'], $contentBlocks);
    }
}
