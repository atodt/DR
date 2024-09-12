<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

trait ControllerTrait
{
    /**
     * @param string $message
     * @return JsonResponse
     */
    private function sendNotFoundResponse(string $message = ''): JsonResponse
    {
        $response = [];
        if ('' !== $message) {
            $response['error'] = $message;
        }

        return $this->json($response, Response::HTTP_NOT_FOUND);
    }
}
