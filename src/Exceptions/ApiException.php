<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Exceptions;

use Saloon\Http\Response;
use Throwable;

class ApiException extends ForgeException
{
    public function __construct(
        public readonly Response $response,
        ?Throwable $previous = null,
    ) {
        $status = $response->status();
        parent::__construct(
            sprintf('Forge API request failed with status %d.', $status),
            $status,
            $previous,
        );
    }

    public function status(): int
    {
        return $this->response->status();
    }
}
