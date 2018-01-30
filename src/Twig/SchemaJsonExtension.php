<?php
declare(strict_types = 1);
namespace App\Twig;

use App\BlogsService\Domain\Post;
use Twig_Extension;
use Twig_Function;

class SchemaJsonExtension extends Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new Twig_Function('post_schema_data', [$this, 'generatePostSchemaData']),
        ];
    }

    public function generatePostSchemaData(Post $post, string $postUrl): string
    {
        $schemaData['@context'] = 'http://schema.org';
        $schemaData['@type'] = 'Article';
        $schemaData['headline'] = $post->getTitle();
        $schemaData['description'] = $post->getShortSynopsis();
        $schemaData['image'] = $post->getImage()->getUrl(1200, 675);
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

        return json_encode($schemaData);
    }
}
