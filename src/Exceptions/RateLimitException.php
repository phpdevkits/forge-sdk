<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Exceptions;

final class RateLimitException extends ApiException
{
    public function retryAfter(): ?int
    {
        $header = $this->response->headers()->get('Retry-After');

        if (! is_string($header) || ! ctype_digit($header)) {
            return null;
        }

        return (int) $header;
    }
}
