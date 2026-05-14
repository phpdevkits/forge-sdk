<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

use InvalidArgumentException;

final readonly class User
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
    ) {}

    /**
     * Hydrate a User from the `data` object of a JSON:API envelope.
     *
     * @param  array<array-key, mixed>  $data
     */
    public static function from(array $data): self
    {
        $id = $data['id'] ?? null;
        $attributes = $data['attributes'] ?? null;

        if (! is_string($id)) {
            throw new InvalidArgumentException('User: `id` must be a string.');
        }

        if (! is_array($attributes)) {
            throw new InvalidArgumentException('User: `attributes` must be an object.');
        }

        $name = $attributes['name'] ?? null;
        $email = $attributes['email'] ?? null;

        if (! is_string($name)) {
            throw new InvalidArgumentException('User: `attributes.name` must be a string.');
        }

        if (! is_string($email)) {
            throw new InvalidArgumentException('User: `attributes.email` must be a string.');
        }

        return new self($id, $name, $email);
    }
}
