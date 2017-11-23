<?php
declare(strict_types = 1);

namespace App\Twig;

use InvalidArgumentException;
use Twig_Extension;
use Twig_Function;

class GelIconExtension extends Twig_Extension
{
    /** @var string[][] */
    private $usedIcons = [];

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new Twig_Function('gelicons_source', [$this, 'gelIconsSource'], [
                'is_safe' => ['html'],
            ]),
            new Twig_Function('gelicon', [$this, 'gelIcon'], [
                'is_safe' => ['html'],
            ]),
        ];
    }

    public function gelIconsSource()
    {
        if (empty($this->usedIcons)) {
            return '';
        }
        $iconsSource = '<svg xmlns="http://www.w3.org/2000/svg" class="gelicons-source"><defs>';
        foreach ($this->usedIcons as $usedIcon) {
            $iconsSource .= $this->loadSvgSymbol($usedIcon['set'], $usedIcon['icon']);
        }
        $iconsSource .= '</defs></svg>';
        return $iconsSource;
    }

    public function gelIcon(string $set, string $icon, string $extraClasses = ''): string
    {
        $set = preg_replace('/[^A-Za-z0-9_-]/', '', $set);
        $icon = preg_replace('/[^A-Za-z0-9_-]/', '', $icon);
        $this->addIcon($set, $icon);
        $classes = 'gelicon ' . $extraClasses;
        $iconId = "#gelicon--$set--$icon";

        return '<svg class="' . htmlspecialchars($classes, ENT_HTML5) . '"><use xlink:href="' . $iconId . '" /></svg>';
    }

    private function addIcon(string $set, string $icon): void
    {
        $key = "${set}|${icon}";
        if (!isset($this->usedIcons[$key])) {
            $this->usedIcons[$key] = ['set' => $set, 'icon' => $icon];
        }
    }

    private function loadSvgSymbol(string $set, string $icon): string
    {
        $path = dirname(dirname(__DIR__)) . join(DIRECTORY_SEPARATOR, ['', 'resources', 'gelicons', $set, $icon]) . '.svg';
        $contents = file_get_contents($path);
        if (!$contents) {
            throw new InvalidArgumentException("$path does not contain a valid svg");
        }
        return $contents;
    }
}
