<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends AbstractController
{
    /**
     * @param array $result
     * @return JsonResponse
     */
    public function response(array $result): JsonResponse
    {
        if ($result['success']) {
            return $this->json(['response' => $result], Response::HTTP_OK);
        }

        return $this->json(['response' => $result], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
