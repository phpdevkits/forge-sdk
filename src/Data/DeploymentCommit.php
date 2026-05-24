<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

use InvalidArgumentException;
use JsonSerializable;
use Override;

/**
 * The `commit` sub-object on a {@see Deployment}. Every field is nullable —
 * a deployment that isn't tied to a specific commit (e.g. a manual redeploy)
 * may have none of them.
 */
final readonly class DeploymentCommit implements JsonSerializable
{
    public function __construct(
        public ?string $hash,
        public ?string $author,
        public ?string $message,
        public ?string $branch,
    ) {}

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function from(array $data): self
    {
        return new self(
            hash: self::optionalString($data, 'hash'),
            author: self::optionalString($data, 'author'),
            message: self::optionalString($data, 'message'),
            branch: self::optionalString($data, 'branch'),
        );
    }

    /**
     * @param  array<array-key, mixed>  $data
     */
    private static function optionalString(array $data, string $key): ?string
    {
        $value = $data[$key] ?? null;
        if ($value === null) {
            return null;
        }

        if (! is_string($value)) {
            throw new InvalidArgumentException(sprintf('DeploymentCommit `%s` must be a string or null.', $key));
        }

        return $value;
    }

    /**
     * @return array{hash: ?string, author: ?string, message: ?string, branch: ?string}
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'hash' => $this->hash,
            'author' => $this->author,
            'message' => $this->message,
            'branch' => $this->branch,
        ];
    }
}
