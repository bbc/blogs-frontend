<?php
declare(strict_types = 1);

namespace Tests\App\Ds\Post\ContentBlock;

use App\BlogsService\Domain\ContentBlock\Social;
use Symfony\Component\DomCrawler\Crawler;
use Tests\App\BaseTemplateTestCase;
use Tests\App\TwigEnvironmentProvider;

class SocialBlockTest extends BaseTemplateTestCase
{
    /**
     * @dataProvider socialBlockProvider
     */
    public function testWithAltText(bool $withAlt, string $expectedText)
    {
        $crawler = $this->createCrawler($withAlt);
        $socialLink = $crawler->filterXPath('//a');

        $this->assertEquals($expectedText, $socialLink->text());
    }

    public function socialBlockProvider(): array
    {
        return [
            'withAlt' => [true, 'Here is some alt text'],
            'withoutAlt' => [false, 'some-social-url'],
        ];
    }

    private function createCrawler(bool $withAlt): Crawler
    {
        $link = 'some-social-url';
        $alt = $withAlt ? 'Here is some alt text' : '';
        $socialBlock = new Social($link, $alt);

        $presenterFactory = TwigEnvironmentProvider::dsPresenterFactory();
        $presenter = $presenterFactory->postFullPresenter([$socialBlock], []);

        return $this->presenterCrawler($presenter);
    }
}
