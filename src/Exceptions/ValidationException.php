<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Exceptions;

final class ValidationException extends ApiException
{
    /**
     * @return array<string, list<string>>
     */
    public function errors(): array
    {
        $raw = $this->response->json('errors');

        if (! is_array($raw)) {
            return [];
        }

        $errors = [];

        foreach ($raw as $field => $messages) {
            if (! is_string($field)) {
                continue;
            }

            if (! is_array($messages)) {
                continue;
            }

            $stringMessages = [];

            foreach ($messages as $message) {
                if (is_string($message)) {
                    $stringMessages[] = $message;
                }
            }

            $errors[$field] = $stringMessages;
        }

        return $errors;
    }

    /**
     * @return list<string>
     */
    public function errorsFor(string $field): array
    {
        return $this->errors()[$field] ?? [];
    }

    public function firstError(string $field): ?string
    {
        return $this->errorsFor($field)[0] ?? null;
    }
}
