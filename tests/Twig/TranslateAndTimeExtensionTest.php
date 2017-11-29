<?php
declare(strict_types = 1);

namespace Tests\App\Twig;

use App\Translate\TranslatableTrait;
use App\Translate\TranslateProvider;
use App\Twig\TranslateAndTimeExtension;
use DateTime;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use ReflectionClass;
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

    public function testLocalDateIntl()
    {
        $mockTranslate = $this->createMock(Translate::class);
        $mockTranslate->expects($this->once())->method('getLocale')
            ->willReturn('cy_GB');

        $boundFunction = $this->boundLocalDateIntl($mockTranslate);
        $dateTime = new DateTime('2017-08-11 06:00:00');
        $result = $boundFunction($dateTime, 'EEE dd MMMM yyyy, HH:mm');
        $this->assertEquals('Gwen 11 Awst 2017, 06:00', $result);
    }

    private function boundLocalDateIntl(Translate $translate): callable
    {
        $translateProvider = $this->createMock(TranslateProvider::class);
        $translateProvider->method('getTranslate')->willReturn($translate);
        $translatable = $this->getMockForTrait(TranslatableTrait::class);

        $reflection = new ReflectionClass($translatable);
        $translateProperty = $reflection->getProperty('translateProvider');
        $translateProperty->setAccessible(true);

        $translateProperty->setValue($translatable, $translateProvider);

        // Define a closure that will call the protected method using "this".
        $barCaller = function (...$args) {
            return $this->localDateIntl(...$args);
        };
        // Bind the closure to $translatable's scope.
        return $barCaller->bindTo($translatable, $translatable);
    }
}
