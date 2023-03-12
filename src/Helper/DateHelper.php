<?php

namespace Paywa\CommissionTask\Helper;

use DateTime;
use DateInterval;

class DateHelper
{
    /**
     * @param DateTime $date
     * @return DateTime
     */
    public static function getSameWeekMonday(DateTime $date): DateTime
    {
        /**
         * `$dayOfWeek` is numeric representation of the day of the week.
         * 0 (for Sunday) through 6 (for Saturday).
         * Monday is 1, so we need to count how many days to subtract from given date.
         */
        $dayOfWeek = intval($date->format('w'));
        if (0 === $dayOfWeek) {
            $intervalString = '5 days';
        } else {
            $intervalString = ($dayOfWeek - 1) . ' days';
        }
        $interval = DateInterval::createFromDateString($intervalString);

        return $date->sub($interval);
    }
}
