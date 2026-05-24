<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

use InvalidArgumentException;
use JsonSerializable;
use Override;

/**
 * The `repository` sub-object on a {@see Site}. Only `provider` is always
 * present; the rest are null until a repository is connected.
 */
final readonly class SiteRepository implements JsonSerializable
{
    public function __construct(
        public string $provider,
        public ?string $url,
        public ?string $branch,
        public ?string $status,
    ) {}

    /**
     * @param  array<array-key, mixed>  $data
     */
    public static function from(array $data): self
    {
        $provider = $data['provider'] ?? null;
        if (! is_string($provider)) {
            throw new InvalidArgumentException('SiteRepository `provider` must be a string.');
        }

        return new self(
            provider: $provider,
            url: self::optionalString($data, 'url'),
            branch: self::optionalString($data, 'branch'),
            status: self::optionalString($data, 'status'),
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
            throw new InvalidArgumentException(sprintf('SiteRepository `%s` must be a string or null.', $key));
        }

        return $value;
    }

    /**
     * @return array{provider: string, url: ?string, branch: ?string, status: ?string}
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'provider' => $this->provider,
            'url' => $this->url,
            'branch' => $this->branch,
            'status' => $this->status,
        ];
    }
}
