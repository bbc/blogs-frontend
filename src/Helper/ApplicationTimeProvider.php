<?php
declare(strict_types = 1);

namespace App\Helper;

use Cake\Chronos\Chronos;
use Cake\Chronos\Date;
use DateTimeZone;

class ApplicationTimeProvider
{
    /** @var Chronos|null  */
    private static $appTime;

    /** @var Chronos|null */
    private static $localTime;

    /** @var DateTimeZone|null */
    private static $localTimeZone;

    public static function getTime(): Chronos
    {
        if (null === static::$appTime) {
            static::setDateTime();
        }

        return static::$appTime;
    }

    public static function getLocalTime(): Chronos
    {
        if (null === static::$localTime) {
            $time = self::getTime();
            static::$localTime = $time->setTimezone(self::getLocalTimeZone());
        }

        return static::$localTime;
    }

    /**
     * This is used to return a Chronos object that's an hour offset for
     * querying iSite publihsed time field where "now" can actually be an hour off
     * @return Chronos
     */
    public static function getTimeOffsetByCurrentDSTOffset(): Chronos
    {
        if (self::getLocalTime()->format('I')) {
            // We are in DST. Add an hour to times for certain queries.
            return Chronos::createFromTimestamp(self::getTime()->addHour()->getTimestamp(), 'UTC');
        }
        return self::getTime();
    }

    public static function getLocalTimeZone(): DateTimeZone
    {
        if (null === self::$localTimeZone) {
            self::setLocalTimeZone();
        }
        return self::$localTimeZone;
    }

    public static function setDateTime(Chronos $time = null)
    {
        static::$localTime = null;
        if ($time === null) {
            static::$appTime = Chronos::now();
            return;
        }
        static::$appTime = $time->setTimezone(new DateTimeZone('UTC'));
        Chronos::setTestNow(static::$appTime);
        Date::setTestNow(static::$appTime);
    }

    public static function setLocalTimeZone(string $timezoneString = 'Europe/London'): void
    {
        self::$localTimeZone = new DateTimeZone($timezoneString);
        self::$localTime = null;
    }

    public static function clearDateTime()
    {
        static::$appTime = null;
        static::$localTimeZone = null;
        static::$localTime = null;
        Chronos::setTestNow();
        Date::setTestNow();
    }
}
