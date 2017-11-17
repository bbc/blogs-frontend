<?php
declare(strict_types = 1);
namespace App\BlogsService\Domain\ValueObject;

class Comments
{
    private $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }
}
