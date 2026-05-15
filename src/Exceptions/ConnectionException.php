<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Exceptions;

use Throwable;

final class ConnectionException extends ForgeException
{
    public function __construct(string $message = '', ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
