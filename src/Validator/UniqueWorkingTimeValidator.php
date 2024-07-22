<?php

declare(strict_types=1);

namespace App\Validator;

use App\Entity\WorkingTime;
use App\Repository\WorkingTimeRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UniqueWorkingTimeValidator extends ConstraintValidator
{
    public function __construct(private WorkingTimeRepository $workingTimeRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$value instanceof WorkingTime) {
            return;
        }

        $employee = $value->getEmployee();
        $startDate = $value->getStartDate();

        $existingWorkingTime = $this->workingTimeRepository->findOneBy([
            'employee' => $employee,
            'date' => $startDate,
        ]);

        if ($existingWorkingTime) {
            $this->context->buildViolation('An entry for this employee already exists for the date {{ date }}.')
                ->setParameter('{{ date }}', $startDate->format('Y-m-d'))
                ->atPath('startDate')
                ->addViolation();
        }
    }
}
