<?php
declare(strict_types = 1);

namespace Tests\App\Twig;

use App\Twig\GelIconExtension;
use PHPUnit\Framework\TestCase;

class GelIconExtensionTest extends TestCase
{
    public function testGelicon()
    {
        $extension = new GelIconExtension();
        $expectedPattern = '$<svg class="gelicon gelicon--alpha">.+</svg>$';
        $html = $extension->gelIcon('core', 'search', 'gelicon--alpha');
        $this->assertRegExp($expectedPattern, $html);

        $expectedUse = '$<use xlink:href="#gelicon--core--search" />$';
        $this->assertRegExp($expectedUse, $html);
    }
}
