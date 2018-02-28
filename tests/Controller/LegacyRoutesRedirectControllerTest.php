<?php
declare(strict_types = 1);

namespace Tests\App\Controller;

use Tests\App\BaseWebTestCase;

/**
 * @covers \App\Controller\LegacyRoutesRedirectController
 */
class LegacyRoutesRedirectControllerTest extends BaseWebTestCase
{
    /**
     * @dataProvider redirectProvider
     */
    public function testGuidRedirectsCorrectlyGenerated(string $legacyRoute, string $newRoute)
    {
        $this->client->request('GET', $legacyRoute);
        $this->assertRedirectTo($this->client, 301, $newRoute);
    }

    public function redirectProvider(): array
    {
        return [
            'postRedirect' => ['/blogs/5live/posts/Caught-in-the-bus-lane', '/blogs/5live/entries/f547edec-0c9e-3e41-b194-0a8d9f11c45f'],
            'authorRedirect' => ['/blogs/aboutthebbc/authors/Hannah_Khalil', '/blogs/aboutthebbc/authors/529aab97-98a9-359e-83b1-dee10a3274f9'],
        ];
    }
}
