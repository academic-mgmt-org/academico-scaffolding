<?php

namespace App\Exceptions;

use RuntimeException;

class GatewayRpcException extends RuntimeException
{
    public function __construct(
        public readonly string $operation,
        public readonly int $statusCode,
        string $details = '',
    ) {
        $safeDetails = trim($details) !== '' ? trim($details) : 'sin detalle';

        parent::__construct("{$operation} falló por gRPC ({$statusCode}): {$safeDetails}");
    }

    public function isUnauthenticated(): bool
    {
        return $this->statusCode === \Grpc\STATUS_UNAUTHENTICATED;
    }
}
