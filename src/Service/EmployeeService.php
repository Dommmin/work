<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EmployeeService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private EmployeeRepository $employeeRepository,
        private ValidatorInterface $validator,
    ) {
    }

    public function createEmployee(Request $request): array
    {
        $data = json_decode($request->getContent(), true);
        $employee = $this->employeeRepository->create($data);
        $violations = $this->validator->validate($employee);
        if (count($violations) > 0) {
            return $this->getValidationErrors($violations);
        }

        $this->entityManager->persist($employee);
        $this->entityManager->flush();

        return [
            'success' => true,
            'id' => $employee->getUuid(),
        ];
    }

    private function getValidationErrors($violations): array
    {
        $errors = [];

        foreach ($violations as $violation) {
            $propertyPath = $violation->getPropertyPath();
            $errors[$propertyPath] = $violation->getMessage();
        }

        return [
            'success' => false,
            'errors' => $errors,
        ];
    }
}
