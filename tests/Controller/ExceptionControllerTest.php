<?php
declare(strict_types = 1);
namespace Tests\App\Controller;

use Tests\App\BaseWebTestCase;

/**
 * @covers \App\Controller\ExceptionController
 */
class ExceptionControllerTest extends BaseWebTestCase
{
    public function test404Error()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/_error/404');

        // The test page for all errors, always returns a 200 status
        $this->assertResponseStatusCode($client, 200);

        $this->assertHasRequiredResponseHeaders($client, 'max-age=60, public');

        $this->assertEquals(
            'Sorry, that page was not found',
            $crawler->filter('.blogs-page h1')->text()
        );

        $this->assertEquals(
            '/assets/images/error/clanger_error.gif',
            $crawler->filter('.blogs-page img')->attr('src')
        );
    }

    public function test500Error()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/_error/500');

        // The test page for all errors, always returns a 200 status
        $this->assertResponseStatusCode($client, 200);

        $this->assertHasRequiredResponseHeaders($client, 'no-cache, private');

        $this->assertEquals(
            'Internal server error',
            $crawler->filter('.blogs-page h1')->text()
        );

        $this->assertEquals(
            '/assets/images/error/clanger_error.gif',
            $crawler->filter('.blogs-page img')->attr('src')
        );
    }
}
