<?php

declare(strict_types=1);

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Throwable;

class JsonErrorController
{
    public function show(Throwable $exception, LoggerInterface $logger): JsonResponse
    {
        return new JsonResponse([
                'success' => false,
                'data' => $exception->getMessage(),
                'code' => $exception->getPrevious()?->getCode() ?? 0,
            ]
        );
    }
}
