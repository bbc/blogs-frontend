<?php
declare(strict_types = 1);

namespace App\BlogsService\Domain;

class Image
{
    /** @var string */
    private $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * Gets the URL of the image for a specific width and height.
     * @param int $width
     * @param int|null $height
     * @return string
     */
    public function getUrl(int $width, ?int $height = null): string
    {
        $host = 'https://ichef.bbci.co.uk';

        if (empty($height)) {
            $dimensions = $width . 'xn';
        } else {
            $dimensions = $width . 'x' . $height;
        }

        $this->id = $this->appendExtension($this->id);

        return $host . '/images/ic/' . $dimensions . '/' . $this->id;
    }

    public function appendExtension(string $string): string
    {
        if (preg_match("/(\.jpg|\.jpeg|\.gif|\.png)$/", $string) == 0) {
            return $string . '.jpg';
        }

        return $string;
    }
}
