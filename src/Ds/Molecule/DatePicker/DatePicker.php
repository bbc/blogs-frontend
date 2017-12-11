<?php
declare(strict_types = 1);

namespace App\Ds\Molecule\DatePicker;

use Cake\Chronos\Chronos;
use Cake\Chronos\ChronosInterface;
use Cake\Chronos\Date;

class DatePicker
{
    /** @var int */
    private $year;

    /** @var int */
    private $month;

    /** @var Chronos */
    private $latestPostDate;

    /** @var Chronos */
    private $oldestPostDate;

    /** @var int[] */
    private $monthlyTotals;

    /** @var Date */
    private $chosenMonthYear;

    public function __construct(int $year, int $month, Chronos $latestPostDate, Chronos $oldestPostDate, array $monthlyTotals)
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

    public function getLatestPostDate(): ChronosInterface
    {
        return $this->latestPostDate;
    }

    public function getOldestPostDate(): ChronosInterface
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
