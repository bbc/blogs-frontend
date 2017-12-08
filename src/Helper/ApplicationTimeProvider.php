<?php
declare(strict_types = 1);

namespace App\Helper;

use Cake\Chronos\Chronos;
use Cake\Chronos\Date;

class ApplicationTimeProvider
{
    public static function setTestDateTime(Chronos $time)
    {
        Chronos::setTestNow($time);
        Date::setTestNow($time);
    }

    public static function clearTestDateTime()
    {
        Chronos::setTestNow();
        Date::setTestNow();
    }
}
