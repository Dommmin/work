<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\EmployeeService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EmployeeController extends ApiController
{
    public function __construct(private readonly EmployeeService $employeeService)
    {
    }

    #[Route('v1/employees', name: 'v1.employee.create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $result = $this->employeeService->createEmployee($request);

        return $this->response($result);
    }
}
