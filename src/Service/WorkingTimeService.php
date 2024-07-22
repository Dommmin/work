<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\DaySummaryDto;
use App\Dto\ErrorDto;
use App\Dto\MonthSummaryDto;
use App\Entity\WorkingTime;
use App\Repository\EmployeeRepository;
use App\Repository\WorkingTimeRepository;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class WorkingTimeService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private EmployeeRepository $employeeRepository,
        private WorkingTimeRepository $workingTimeRepository,
        private ValidatorInterface $validator,
    ) {
    }

    public function createWorkingTime(Request $request): array
    {
        $data = json_decode($request->getContent(), true);
        $employee = $this->employeeRepository->findOneBy(['uuid' => $data['employee'] ?? null]);

        if (! $employee) {
            return (new ErrorDto(false, ['employee' => 'Employee not found']))->toArray();
        }

        try {
            $startDate = Carbon::parse($data['startDate']);
            $endDate = Carbon::parse($data['endDate']);
        } catch (\Exception $e) {
            return (new ErrorDto(false, ['date' => 'Invalid date format']))->toArray();
        }

        $data['employee'] = $employee;
        $data['startDate'] = $startDate;
        $data['endDate'] = $endDate;

        $workingTime = $this->workingTimeRepository->create($data);
        $violations = $this->validator->validate($workingTime);

        if (count($violations) > 0) {
            return $this->getValidationErrors($violations)->toArray();
        }

        $this->entityManager->persist($workingTime);
        $this->entityManager->flush();

        return ['success' => true, 'message' => 'Working time created'];
    }

    public function daySummary(Request $request): array
    {
        $errors = [];

        $queryDate = $request->query->get('date');
        $employeeId = $request->query->get('employee');

        if (! $employeeId) {
            $errors['employee'] = 'Employee is required';
        }

        if (! $queryDate) {
            $errors['date'] = 'Date is required';
        } else {
            $date = \DateTime::createFromFormat('Y-m-d', $queryDate);
            if (! $date) {
                $errors['date'] = 'Date is not valid. Format should be YYYY-MM-DD';
            }
        }

        $employee = $this->employeeRepository->findOneBy(['uuid' => $employeeId]);

        if (! $employee) {
            $errors['employee'] = 'Employee not found';
        }

        if (count($errors) > 0) {
            return (new ErrorDto(false, $errors))->toArray();
        }

        $workingTime = $this->workingTimeRepository->getDaySummaryForEmployee($employee, $queryDate);

        if (! $workingTime) {
            return [
                'success' => true,
                'message' => 'No working time found',
            ];
        }

        $hours = $this->calculateHours($workingTime->getStartDate(), $workingTime->getEndDate());

        return (new DaySummaryDto(
            true,
            $hours * WorkingTime::SALARY_PER_HOUR,
            $hours,
            WorkingTime::SALARY_PER_HOUR
        ))->toArray();
    }

    public function monthSummary(Request $request): array
    {
        $errors = [];

        $queryDate = $request->query->get('date');
        $employeeId = $request->query->get('employee');

        if (! $employeeId) {
            $errors['employee'] = 'Employee is required';
        }

        if (! $queryDate) {
            $errors['date'] = 'Date is required';
        } else {
            $date = \DateTime::createFromFormat('Y-m', $queryDate);
            if (! $date) {
                $errors['date'] = 'Date is not valid. Format should be YYYY-MM';
            }
        }

        $employee = $this->employeeRepository->findOneBy(['uuid' => $employeeId]);

        if (! $employee) {
            $errors['employee'] = 'Employee not found';
        }

        if (count($errors) > 0) {
            return (new ErrorDto(false, $errors))->toArray();
        }

        $workingTimes = $this->workingTimeRepository->getMonthSummaryForEmployee($employee, $queryDate);
        $totalHours = array_reduce($workingTimes, function ($carry, WorkingTime $workingTime) {
            return $carry + $this->calculateHours($workingTime->getStartDate(), $workingTime->getEndDate());
        }, 0);

        $regularHours = WorkingTime::HOURS_PER_MONTH;
        $overtimeHours = max(0, $totalHours - $regularHours);
        $totalHours = $totalHours >= WorkingTime::HOURS_PER_MONTH ? $regularHours : $totalHours;

        return (new MonthSummaryDto(
            true,
            $totalHours,
            $overtimeHours,
            $regularHours * WorkingTime::SALARY_PER_HOUR +
                    $overtimeHours * WorkingTime::SALARY_PER_HOUR * WorkingTime::PERCENTAGE_SALARY_FOR_OVERTIME,
            WorkingTime::SALARY_PER_HOUR,
            WorkingTime::PERCENTAGE_SALARY_FOR_OVERTIME
        ))->toArray();
    }

    private function getValidationErrors(ConstraintViolationListInterface $violations): ErrorDto
    {
        $errors = [];

        foreach ($violations as $violation) {
            $propertyPath = $violation->getPropertyPath();
            $errors[$propertyPath] = $violation->getMessage();
        }

        return new ErrorDto(false, $errors);
    }

    private function calculateHours(\DateTimeInterface $startDate, \DateTimeInterface $endDate): float
    {
        $interval = $endDate->diff($startDate);

        $hours = $interval->h;
        $hours += $interval->i / 60;

        return $this->roundToNearestHalfHour($hours);
    }

    private function roundToNearestHalfHour(float $hours): float
    {
        $wholeHours = floor($hours);
        $minutes = ($hours - $wholeHours) * 60;

        return match (true) {
            $minutes < 15 => $wholeHours,
            $minutes < 45 => $wholeHours + 0.5,
            default => $wholeHours + 1,
        };
    }
}
