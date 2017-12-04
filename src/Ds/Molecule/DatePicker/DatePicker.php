<?php
declare(strict_types = 1);

namespace App\Ds\Molecule\DatePicker;

use Cake\Chronos\Date;
use DateTimeImmutable;

class DatePicker
{
    /** @var int */
    private $year;

    /** @var int */
    private $month;

    /** @var Date */
    private $latestPostDate;

    /** @var Date */
    private $oldestPostDate;

    /** @var int[] */
    private $monthlyTotals;

    /** @var Date */
    private $chosenMonthYear;

    public function __construct(int $year, int $month, DateTimeImmutable $latestPostDate, DateTimeImmutable $oldestPostDate, array $monthlyTotals)
    {
        $this->year = $year;
        $this->month = $month;
        $this->latestPostDate = $latestPostDate;
        $this->oldestPostDate = $oldestPostDate;
        $this->monthlyTotals = $monthlyTotals;
        $this->chosenMonthYear = Date::create($year, $month, 2);
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function getMonth(): int
    {
        return $this->month;
    }

    public function getLatestPostDate(): DateTimeImmutable
    {
        return $this->latestPostDate;
    }

    public function getOldestPostDate(): DateTimeImmutable
    {
        return $this->oldestPostDate;
    }

    /** @return int[] */
    public function getMonthlyTotals(): array
    {
        return $this->monthlyTotals;
    }

    public function getChosenMonthYear(): Date
    {
        return $this->chosenMonthYear;
    }
}
