<?php
declare(strict_types = 1);

namespace Tests\App\Ds\Molecule\DatePicker;

use App\BlogsService\Domain\Blog;
use App\Ds\Molecule\DatePicker\DatePicker;
use Cake\Chronos\Chronos;
use Symfony\Component\DomCrawler\Crawler;
use Tests\App\BaseTemplateTestCase;
use Tests\App\TwigEnvironmentProvider;

class DatePickerTest extends BaseTemplateTestCase
{
    public function testDatePickerDisplaysRangeAndCounts()
    {
        $crawler = $this->createCrawler(
            2017,
            7,
            Chronos::create(2017, 8, 15, 19, 45),
            Chronos::create(2009, 3, 28, 12, 00),
            $this->setupMonthlyCountsArray([2, 4, 0, 4, 3, 8, 1, 4, 0, 0, 0, 0])
        );

        $years = $crawler->filterXPath('//li[contains(@class, "bbc-datepicker__box-year-number")]');
        $months = $crawler->filterXPath('//li[contains(@class, "bbc-datepicker__box-month-name")]');

        $this->assertEquals(9, $years->count());
        $this->assertEquals(12, $months->count());

        $this->assertEquals('2017', trim($years->first()->text()));
        $this->assertEquals('2009', trim($years->last()->text()));

        $this->assertEquals('January (2)', trim($months->first()->text()));
        $this->assertEquals('December (0)', trim($months->last()->text()));
    }

    private function createCrawler(int $year, int $month, Chronos $latestPostDate, Chronos $oldestPostDate, array $monthlyTotals): Crawler
    {
        $blog = $this->createMock(Blog::class);
        $blog->method('getId')->willReturn('theblogid');

        $datePicker = new DatePicker($year, $month, $latestPostDate, $oldestPostDate, $monthlyTotals);

        $presenterFactory = TwigEnvironmentProvider::dsPresenterFactory();
        $presenter = $presenterFactory->datePickerPresenter($blog, $datePicker);

        return $this->presenterCrawler($presenter);
    }

    /**
     * Make the array of monthly post counts 1-indexed, as per array returned from PostService
     *
     * @return int[] */
    private function setupMonthlyCountsArray(array $counts): array
    {
        return array_combine(range(1, count($counts)), $counts);
    }
}
