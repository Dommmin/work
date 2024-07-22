<?php

declare(strict_types=1);

namespace App\Dto;

class DaySummaryDto
{
    public function __construct(
        public bool $success,
        public float $totalDaySalary,
        public float $totalDayHours,
        public float $salaryPerHour
    ) {
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'totalDaySalary' => $this->totalDaySalary.' PLN',
            'totalDayHours' => $this->totalDayHours,
            'salaryPerHour' => $this->salaryPerHour.' PLN',
        ];
    }
}
