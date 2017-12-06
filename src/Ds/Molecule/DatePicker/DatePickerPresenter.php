<?php
declare(strict_types = 1);

namespace App\Ds\Molecule\DatePicker;

use App\BlogsService\Domain\Blog;
use App\Ds\Presenter;
use Cake\Chronos\Date;
use InvalidArgumentException;

class DatePickerPresenter extends Presenter
{
    /** @var Blog */
    private $blog;

    /** @var DatePicker */
    private $datePicker;

    public function __construct(Blog $blog, DatePicker $datePicker, array $options = [])
    {
        parent::__construct($options);

        $this->blog = $blog;
        $this->datePicker = $datePicker;
    }

    public function getBlogId(): string
    {
        return $this->blog->getId();
    }

    public function getChosenMonthYear(): Date
    {
        return $this->datePicker->getChosenMonthYear();
    }

    /** @return Date[] */
    public function getMonthsOfTheYear(): array
    {
        $months = [];
        for ($i = 1; $i < 13; $i++) {
            $months[] = Date::create($this->datePicker->getYear(), $i, 2);
        }

        return $months;
    }

    public function getPostsYearRange(): array
    {
        $oldestYear = (int) $this->datePicker->getOldestPostDate()->format('Y');
        $latestYear = (int) $this->datePicker->getLatestPostDate()->format('Y');

        $years = [];
        for ($year = $oldestYear; $year <= $latestYear; $year++) {
            $years[] = $year;
        }

        return $years;
    }

    public function getMonthlyTotal(int $month): int
    {
        if ($month < 1 || $month > 12) {
            throw new InvalidArgumentException('Invalid month number supplied.');
        }

        return $this->datePicker->getMonthlyTotals()[$month];
    }
}
