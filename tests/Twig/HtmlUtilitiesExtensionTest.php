<?php
declare(strict_types = 1);
namespace Tests\App\Twig;

use App\Twig\HtmlUtilitiesExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Asset\Packages;

class HtmlUtilitiesExtensionTest extends TestCase
{
    public function testAssetJs()
    {
        $mockPackages = $this->createMock(Packages::class);
        $mockPackages->expects($this->once())->method('getUrl')
            ->with('some/example/path.js')
            ->willReturn('some/example/path-123');

        $extension = new HtmlUtilitiesExtension($mockPackages);
        $this->assertSame('some/example/path-123', $extension->assetJs('some/example/path.js'));
    }

    public function testBuildCssClasses()
    {
        $extension = new HtmlUtilitiesExtension(
            $this->createMock(Packages::class)
        );

        $this->assertSame('foo baz qux', $extension->buildCssClasses([
            'foo' => true,
            'bar' => false,
            'baz qux' => true,
        ]));
    }
}
