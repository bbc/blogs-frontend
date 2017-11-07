<?php
declare(strict_types = 1);
namespace Tests\App;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class BaseWebTestCase extends WebTestCase
{
    public function assertResponseStatusCode($client, $expectedCode)
    {
        $actualCode = $client->getResponse()->getStatusCode();
        $this->assertEquals($expectedCode, $actualCode, sprintf(
            'Failed asserting that the response status code "%s" matches expected "%s"',
            $actualCode,
            $expectedCode
        ));
    }

    public function assertRedirectTo($client, $code, $expectedLocation)
    {
        $this->assertResponseStatusCode($client, $code);
        $this->assertEquals($expectedLocation, $client->getResponse()->headers->get('location'));
    }

    public function assertHasRequiredResponseHeaders($client, $cacheControl = 'max-age=300, public', $contentLanguage = null)
    {
        $this->assertEquals($cacheControl, $client->getResponse()->headers->get('Cache-Control'));
        $this->assertArraySubset(['X-CDN', 'X-BBC-Edge-Scheme'], $client->getResponse()->getVary());
        $this->assertEquals('IE=edge', $client->getResponse()->headers->get('X-UA-Compatible'));
        $this->assertEquals('blogs-frontend', $client->getResponse()->headers->get('X-Webapp'));
        $this->assertEquals('stale-while-revalidate=30', $client->getResponse()->headers->get('X-Cache-Control'));
        if (isset($contentLanguage)) {
            $this->assertEquals($contentLanguage, $client->getResponse()->headers->get('Content-Language'));
        } else {
            $this->assertNotEmpty($client->getResponse()->headers->get('Content-Language'));
        }
    }

    public function extractIstatsLabels($crawler)
    {
        $labels = [];
        $extractedValues = $crawler->filter("orbit-template-params")->extract(['data-values']);
        $labelsObject = json_decode($extractedValues[0]);
        foreach ($labelsObject->analyticsLabels as $item) {
            $labels[$item->key] = urldecode($item->value);
        }
        return $labels;
    }
}
