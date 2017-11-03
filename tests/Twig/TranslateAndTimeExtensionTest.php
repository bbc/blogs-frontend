<?php
declare(strict_types = 1);

namespace Tests\App\Twig;

use App\Translate\TranslateProvider;
use App\Twig\TranslateAndTimeExtension;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use RMP\Translate\Translate;

class TranslateAndTimeExtensionTest extends TestCase
{
    /** @var PHPUnit_Framework_MockObject_MockObject|Translate */
    private $mockTranslate;

    /** @var TranslateAndTimeExtension */
    private $translateAndTimeExtension;

    public function setUp()
    {
        $this->mockTranslate = $this->createMock(Translate::class);
        $translateProvider = $this->createMock(TranslateProvider::class);
        $translateProvider->method('getTranslate')->willReturn($this->mockTranslate);
        $this->translateAndTimeExtension = new TranslateAndTimeExtension($translateProvider);
    }

    public function testTrWrapper()
    {
        $this->mockTranslate->expects($this->once())
            ->method('translate')
            ->with('wibble', ['%count%' => 'eleventy'], 110)
            ->willReturn('Utter Nonsense');
        $result = $this->translateAndTimeExtension->trWrapper('wibble', ['%count%' => 'eleventy'], 110);
        $this->assertEquals('Utter Nonsense', $result);
    }
}
