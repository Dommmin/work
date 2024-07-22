<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\WorkingTimeService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class WorkingTimeController extends ApiController
{
    public function __construct(private readonly WorkingTimeService $workingTimeService)
    {
    }

    /**
     * @throws \Exception
     */
    #[Route('v1/working-times', name: 'v1.working-time.create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $result = $this->workingTimeService->createWorkingTime($request);

        return $this->response($result);
    }

    /**
     * @throws \Exception
     */
    #[Route('v1/working-times/day-summary', name: 'v1.working-time-day-summary.get', methods: ['GET'])]
    public function daySummary(Request $request): JsonResponse
    {
        $result = $this->workingTimeService->daySummary($request);

        return $this->response($result);
    }

    /**
     * @throws \Exception
     */
    #[Route('v1/working-times/month-summary', name: 'v1.working-time-month-summary.get', methods: ['GET'])]
    public function monthSummary(Request $request): JsonResponse
    {
        $result = $this->workingTimeService->monthSummary($request);

        return $this->response($result);
    }
}