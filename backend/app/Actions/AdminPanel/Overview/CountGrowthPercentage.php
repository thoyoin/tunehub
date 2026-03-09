<?php

declare(strict_types=1);

namespace App\Actions\AdminPanel\Overview;

class CountGrowthPercentage
{
    public function handle($currentMonth, $pastMonth): float|int
    {
        if ($pastMonth == 0) {
            $growth = $currentMonth > 0 ? 100 : 0;
        } else {
            $growth = ($currentMonth - $pastMonth) / $pastMonth * 100;
        }

        return $growth;
    }
}
