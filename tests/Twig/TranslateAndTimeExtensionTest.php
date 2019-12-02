<?php
declare(strict_types = 1);

namespace Tests\App\Twig;

use App\Translate\TranslatableTrait;
use App\Twig\TranslateAndTimeExtension;
use DateTime;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use ReflectionClass;
use Symfony\Component\Translation\TranslatorInterface;

class TranslateAndTimeExtensionTest extends TestCase
{
    /** @var PHPUnit_Framework_MockObject_MockObject|TranslatorInterface */
    private $mockTranslate;

    /** @var TranslateAndTimeExtension */
    private $translateAndTimeExtension;

    public function setUp()
    {
        $this->mockTranslate = $this->createMock(TranslatorInterface::class);
        $this->translateAndTimeExtension = new TranslateAndTimeExtension($this->mockTranslate);
    }

    public function testTrWrapper()
    {
        $this->mockTranslate->expects($this->once())
            ->method('trans')
            ->with('wibble %count%', ['%count%' => 'eleventy'], null)
            ->willReturn('Utter Nonsense');
        $result = $this->translateAndTimeExtension->trWrapper('wibble', ['%count%' => 'eleventy'], 110);
        $this->assertEquals('Utter Nonsense', $result);
    }

    public function testLocalDateIntl()
    {
        $mockTranslate = $this->createMock(TranslatorInterface::class);
        $mockTranslate->expects($this->once())->method('getLocale')
            ->willReturn('cy_GB');

        $boundFunction = $this->boundLocalDateIntl($mockTranslate);
        $dateTime = new DateTime('2017-08-11 06:00:00', new \DateTimeZone('Europe/London'));
        $result = $boundFunction($dateTime, 'EEE dd MMMM yyyy, HH:mm');
        $this->assertEquals('Gwen 11 Awst 2017, 06:00', $result);
    }

    private function boundLocalDateIntl(TranslatorInterface $translate): callable
    {
        $translatable = $this->getMockForTrait(TranslatableTrait::class);

        $reflection = new ReflectionClass($translatable);
        $translateProperty = $reflection->getProperty('translator');
        $translateProperty->setAccessible(true);

        $translateProperty->setValue($translatable, $translate);

        // Define a closure that will call the protected method using "this".
        $barCaller = function (...$args) {
            return $this->localDateIntl(...$args);
        };
        // Bind the closure to $translatable's scope.
        return $barCaller->bindTo($translatable, $translatable);
    }
}
