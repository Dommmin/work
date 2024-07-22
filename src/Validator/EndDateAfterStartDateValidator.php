<?php

declare(strict_types=1);

namespace App\Validator;

use App\Entity\WorkingTime;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class EndDateAfterStartDateValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (! $value instanceof WorkingTime) {
            return;
        }

        $startDate = $value->getStartDate();
        $endDate = $value->getEndDate();

        if ($endDate <= $startDate) {
            $this->context->buildViolation('End date must be after start date.')
                ->setParameter('{{ startDate }}', $startDate->format('Y-m-d H:i'))
                ->setParameter('{{ endDate }}', $endDate->format('Y-m-d H:i'))
                ->atPath('endDate')
                ->addViolation();
        }
    }
}
