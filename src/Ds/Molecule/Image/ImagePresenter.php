<?php
declare(strict_types = 1);
namespace App\Ds\Molecule\Image;

use App\Ds\Presenter;
use App\Ds\InvalidOptionException;
use App\BlogsService\Domain\Image;
use InvalidArgumentException;

class ImagePresenter extends Presenter
{
    /** @inheritdoc */
    protected $options = [
        'is_lazy_loaded' => true,
        'srcsets' => [80, 160, 320, 480, 640, 768, 896, 1008],
        'ratio' => (16 / 9),
        'alt' => '',
    ];

    /** @var Image */
    private $image;

    /** @var int */
    private $defaultWidth;

    /** @var string */
    private $sizes;

    /**
     * ImagePresenter constructor.
     * @param Image $image
     * @param int $defaultWidth
     *        Used to build the src attribute for browsers that don't support srcset/sizes
     * @param array|string $sizes
     *        Used to build the sizes attribute
     * @param array $options
     */
    public function __construct(
        Image $image,
        int $defaultWidth,
        $sizes,
        array $options = []
    ) {
        parent::__construct($options);

        if ((!is_string($sizes) && !is_array($sizes)) ||
            (is_array($sizes) && (!empty($sizes) && array_values($sizes) === $sizes))
        ) {
            throw new InvalidArgumentException("Argument 'sizes' must be either an empty or associative array, or a string");
        }

        $this->image = $image;
        $this->defaultWidth = $defaultWidth;
        $this->sizes = $this->buildSizes($sizes);
    }

    public function getSizes(): string
    {
        return $this->sizes;
    }

    public function getSrc(): string
    {
        return $this->buildSrcUrl($this->defaultWidth);
    }

    public function getSrcsets(): string
    {
        $srcsets = [];

        foreach ($this->getOption('srcsets') as $srcset) {
            $srcsets[] = $this->buildSrcUrl($srcset) . ' ' . $srcset . 'w';
        }

        return implode(', ', $srcsets);
    }

    protected function validateOptions(array $options): void
    {
        parent::validateOptions($options);

        if (!is_bool($options['is_lazy_loaded'])) {
            throw new InvalidOptionException("Option 'is_lazy_loaded' must be a boolean");
        }

        if (!is_array($options['srcsets'])) {
            throw new InvalidOptionException("Option 'srcsets' must be an array");
        }

        foreach ($options['srcsets'] as $srcset) {
            if (!is_numeric($srcset)) {
                throw new InvalidOptionException("Every 'srcsets' element must be numeric");
            }
        }

        if (!is_numeric($options['ratio']) && !is_null($options['ratio'])) {
            throw new InvalidOptionException("Option 'ratio' must be numeric or null");
        }

        if (!is_string($options['alt'])) {
            throw new InvalidOptionException("Option 'alt' must be a string");
        }
    }

    /**
     * @param mixed[]|string $sizes
     * @return string
     */
    private function buildSizes($sizes): string
    {
        if (is_string($sizes)) {
            return $sizes;
        }

        // Sizes must be ordered by largest first as we use min-width
        krsort($sizes, SORT_NUMERIC);

        $parts = [];

        foreach ($sizes as $width => $fraction) {
            $width = ($width / 16);

            if (!is_string($fraction)) {
                // Convert to percentage and append the 'vw'
                $fraction = ($fraction * 100);
                $fraction .= 'vw';
            }

            $parts[] = '(min-width: ' . $width . 'em) ' . $fraction;
        }

        // add the final 100vw in case nothing matched
        $parts[] = '100vw';

        return implode(', ', $parts);
    }

    private function buildSrcUrl(?int $width): string
    {
        if ($this->getOption('ratio')) {
            $height = (int) round($width / $this->getOption('ratio'));
            return $this->image->getUrl($width, $height);
        }

        return $this->image->getUrl($width);
    }
}
