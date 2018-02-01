<?php
declare(strict_types = 1);
namespace App\Twig;

use App\BlogsService\Domain\Post;
use Twig_Extension;
use Twig_Function;

class SchemaJsonExtension extends Twig_Extension
{
    private $schemaSnippets;

    public function __construct()
    {
        $this->schemaSnippets = [];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new Twig_Function('post_schema_data', [$this, 'generatePostSchemaData']),
            new Twig_Function('generate_schema_json', [$this, 'generateSchemaData']),
        ];
    }

    public function generatePostSchemaData(Post $post, string $postUrl)
    {
        $schemaData['@type'] = 'Article';
        $schemaData['headline'] = $post->getTitle();
        $schemaData['description'] = $post->getShortSynopsis();
        $schemaData['image'] = $post->getImage() ? $post->getImage()->getUrl(1200, 675) : '';
        $schemaData['datePublished'] = $post->getPublishedDate()->format('Y-m-d\TH:i:s');
        $schemaData['dateModified'] = $post->getPublishedDate()->format('Y-m-d\TH:i:s');
        $schemaData['author'] = [
            '@type' => 'Person',
            'name' => $post->getAuthor()->getName(),
        ];
        $schemaData['publisher'] = [
            '@type' => 'Organization',
            'name' => 'BBC',
            'logo' => [
                '@type' => 'ImageObject',
                'url' => 'http://ichef.bbci.co.uk/images/ic/1200x675/p01tqv8z.png',
            ],
        ];
        $schemaData['mainEntityOfPage'] = [
            '@type' => 'WebPage',
            '@id' => $postUrl,
        ];

        $this->schemaSnippets[] = $schemaData;
    }

    public function generateSchemaData(): string
    {
        if (empty($this->schemaSnippets)) {
            return '';
        }

        $schemaData['@context'] = 'http://schema.org';

        if (\count($this->schemaSnippets) == 1) {
            return json_encode(array_merge($schemaData, $this->schemaSnippets));
        }

        $schemaData['@graph'] = $this->schemaSnippets;

        return json_encode($schemaData);
    }
}
