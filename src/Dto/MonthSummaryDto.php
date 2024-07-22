<?php

declare(strict_types=1);

namespace App\Dto;

class MonthSummaryDto
{
    public function __construct(
        public bool $success,
        public float $totalRegularHours,
        public float $overtimeHours,
        public float $totalSalary,
        public float $salaryPerHour,
        public float $overtimeMultiplier
    ) {
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'totalRegularHours' => $this->totalRegularHours,
            'salaryPerHour' => $this->salaryPerHour.' PLN',
            'overtimeHours' => $this->overtimeHours,
            'overtimeSalaryPerHour' => $this->salaryPerHour * $this->overtimeMultiplier.' PLN',
            'totalSalary' => $this->totalSalary.' PLN',
        ];
    }
}
