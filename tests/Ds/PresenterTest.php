<?php
declare(strict_types = 1);
namespace Tests\App\Ds;

use App\Ds\Presenter;
use App\Ds\InvalidOptionException;
use PHPUnit\Framework\TestCase;

class PresenterTest extends TestCase
{
    public function testAbstractPresenter()
    {
        $presenter = $this->getMockForAbstractClass(Presenter::class, [], 'TestObjectPresenter');

        $this->assertAttributeEquals([], 'options', $presenter);
        $this->assertSame('test_object', $presenter->getTemplateVariableName());
        $this->assertSame('@Ds/test_object.html.twig', $presenter->getTemplatePath());

        // Assert each presenter generates their own template info
        $presenter = $this->getMockForAbstractClass(Presenter::class, [], 'AnotherTestObjectPresenter');
        $this->assertSame('another_test_object', $presenter->getTemplateVariableName());
        $this->assertSame('@Ds/another_test_object.html.twig', $presenter->getTemplatePath());
    }

    public function testGetOption()
    {
        $presenter = $this->getMockForAbstractClass(Presenter::class, [
            ['optionOne' => 1, 'optionTwo' => 2],
        ]);

        $this->assertSame(1, $presenter->getOption('optionOne'));
        $this->assertSame(2, $presenter->getOption('optionTwo'));
    }

    public function testGetOptionInvalid()
    {
        $presenter = $this->getMockForAbstractClass(Presenter::class, [
            ['optionOne' => 1, 'optionTwo' => 2],
        ]);

        $this->expectException(InvalidOptionException::class);
        $this->expectExceptionMessage(
            'Called getOption with an invalid value. Expected one of "optionOne", "optionTwo" but got "garbage"'
        );

        $presenter->getOption('garbage');
    }

    public function testGetUniqueId()
    {
        $presenter = $this->getMockForAbstractClass(Presenter::class, [], 'TestObjectPresenter');
        $initialId = $presenter->getUniqueId();

        // Assert format
        $this->assertRegExp('/^ds-TestObjectPresenter-[0-9]+$/', $initialId);

        // Assert we get the same value if we call uniqueID multiple times on the same Presenter
        $this->assertSame($initialId, $presenter->getUniqueId());

        // Assert a new presenter gets a different ID
        $secondPresenter = $this->getMockForAbstractClass(Presenter::class, [], 'TestObjectPresenter');
        $this->assertNotEquals($initialId, $secondPresenter->getUniqueId());
    }
}
