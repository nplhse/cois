<?php

namespace App\Helper;

class StatisticsHelper
{
    public static function getPercentage(int $total, int $faction): float
    {
        if ($total > 0) {
            return round(($faction * 100) / $total, 2);
        }

        return 0;
    }
}
