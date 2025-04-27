<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MonthNameExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('month_name', [$this, 'getMonthName']),
        ];
    }

    public function getMonthName(int $month): string
    {
        $monthNames = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];

        return $monthNames[$month] ?? 'Invalid Month';
    }
}