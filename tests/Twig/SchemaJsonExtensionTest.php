<?php
declare(strict_types = 1);
namespace Tests\App\Twig;

use App\Twig\SchemaJsonExtension;
use PHPUnit\Framework\TestCase;
use Tests\App\Builders\PostBuilder;

class SchemaJsonExtensionTest extends TestCase
{
    /** @var SchemaJsonExtension */
    private $extension;

    public function setUp()
    {
        $this->extension = new SchemaJsonExtension();
    }

    public function testNoSchemaElements()
    {
        $result = $this->extension->generateSchemaData();
        $this->assertEquals('', $result);
    }

    public function testSingleSchemaElement()
    {
        $post = PostBuilder::default()->build();
        $this->extension->generatePostSchemaData($post, 'someurl');

        $result = $this->extension->generateSchemaData();

        $this->assertJson($result);
        $this->assertJsonKeyNotExists($result, '@graph');
    }

    public function testMultipleSchemaElements()
    {
        $post = PostBuilder::default()->build();
        $this->extension->generatePostSchemaData($post, 'someurl');
        $this->extension->generatePostSchemaData($post, 'someotherurl');

        $result = $this->extension->generateSchemaData();

        $this->assertJson($result);
        $this->assertJsonKeyExists($result, '@graph');
    }

    public function testNoAuthorNoImage()
    {
        $post = PostBuilder::defaultMinimal()->build();
        $this->extension->generatePostSchemaData($post, 'someurl');

        $result = $this->extension->generateSchemaData();

        $this->assertJson($result);
    }

    private function assertJsonKeyExists(string $json, string $key)
    {
        $this->assertJsonKeyExistance($json, $key, true);
    }

    private function assertJsonKeyNotExists(string $json, string $key)
    {
        $this->assertJsonKeyExistance($json, $key, false);
    }

    private function assertJsonKeyExistance(string $json, string $key, bool $doesExist)
    {
        $jsonObject = json_decode($json, true);
        $doesExist ? $this->assertArrayHasKey($key, $jsonObject) : $this->assertArrayNotHasKey($key, $jsonObject);
    }
}
