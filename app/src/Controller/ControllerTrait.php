<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ControllerTrait
{
    private function sendNotFoundResponse(string $message = ''): JsonResponse
    {
        $response = [];
        if ('' !== $message) {
            $response['error'] = $message;
        }

        return $this->json($response, Response::HTTP_NOT_FOUND);
    }
}
