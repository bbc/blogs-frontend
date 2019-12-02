<?php
declare(strict_types = 1);

namespace Tests\App\Translate;

use App\Translate\TranslatableTrait;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\Translation\TranslatorInterface;

class TranslatableTraitTest extends TestCase
{
    public function testTrBasic()
    {
        $mockTranslate = $this->createMock(TranslatorInterface::class);
        $mockTranslate->expects($this->once())->method('trans')
            ->with('key')
            ->willReturn('output');

        $trFn = $this->boundTr($mockTranslate);

        $this->assertSame('output', $trFn('key'));
    }

    public function testTrSubstitutions()
    {
        $mockTranslate = $this->createMock(TranslatorInterface::class);
        $mockTranslate->expects($this->once())->method('trans')
            ->with('key', ['%sub%' => 'ham'])
            ->willReturn('output');

        $trFn = $this->boundTr($mockTranslate);

        $this->assertSame('output', $trFn('key', ['%sub%' => 'ham']));
    }

    public function testTrPlurals()
    {
        $mockTranslate = $this->createMock(TranslatorInterface::class);
        $mockTranslate->expects($this->once())->method('trans')
            ->with('key %count%', ['%count%' => 2])
            ->willReturn('output');

        $trFn = $this->boundTr($mockTranslate);

        $this->assertSame('output', $trFn('key', 2));
    }

    public function testTrSubstitutionsAndPlurals()
    {
        $mockTranslate = $this->createMock(TranslatorInterface::class);
        $mockTranslate->expects($this->once())->method('trans')
            ->with('key %count%', ['%sub%' => 'ham', '%count%' => 2])
            ->willReturn('output');

        $trFn = $this->boundTr($mockTranslate);

        $this->assertSame('output', $trFn('key', ['%sub%' => 'ham'], 2));
    }

    /**
     * This is funky. It generates a closure that has its scope bound to a
     * mock, which means it has access to call protected functions (i.e. tr).
     * We also need to do some reflection malarkey to set the translateProvider property
     */
    private function boundTr(TranslatorInterface $translate): callable
    {
        $translatable = $this->getMockForTrait(TranslatableTrait::class);

        $reflection = new ReflectionClass($translatable);
        $translateProperty = $reflection->getProperty('translator');
        $translateProperty->setAccessible(true);

        $translateProperty->setValue($translatable, $translate);

        // Define a closure that will call the protected method using "this".
        $barCaller = function (...$args) {
            return $this->tr(...$args);
        };
        // Bind the closure to $translatable's scope.
        return $barCaller->bindTo($translatable, $translatable);
    }
}
