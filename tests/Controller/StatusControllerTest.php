<?php
declare(strict_types = 1);
namespace Tests\App\Controller;

use Tests\App\BaseWebTestCase;

/**
 * @covers App\Controller\StatusController
 */
class StatusControllerTest extends BaseWebTestCase
{
    public function testStatus()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/status');
        $this->assertResponseStatusCode($client, 200);
        $this->assertHasRequiredResponseHeaders($client, 'no-cache, private');
    }

    public function testStatusFromElb()
    {
        $client = static::createClient([], [
            'HTTP_USER_AGENT' => 'ELB-HealthChecker/1.0',
        ]);
        $crawler = $client->request('GET', '/status');

        $this->assertResponseStatusCode($client, 200);
        $this->assertEquals('OK', $client->getResponse()->getContent());
        $this->assertHasRequiredResponseHeaders($client, 'no-cache, private');
    }
}
