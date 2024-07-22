<?php

declare(strict_types=1);

namespace App\Validator;

use App\Entity\WorkingTime;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class WorkingHoursValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (! $value instanceof WorkingTime) {
            return;
        }

        $startDate = $value->getStartDate();
        $endDate = $value->getEndDate();

        if ($startDate->format('Y-m-d') !== $endDate->format('Y-m-d')) {
            $this->context->buildViolation('Start date and end date must be on the same day.')
                ->atPath('endDate')
                ->addViolation();
        }

        if ($startDate <= $endDate) {
            $interval = $startDate->diff($endDate);
            $totalHours = $interval->days * 24 + $interval->h + $interval->i / 60;

            if ($totalHours > WorkingTime::HOURS_PER_DAY) {
                $this->context->buildViolation('The duration between start and end date cannot exceed '.WorkingTime::HOURS_PER_DAY.'hours.')
                    ->atPath('endDate')
                    ->addViolation();
            }
        }
    }
}
