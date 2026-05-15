<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

use DateTimeImmutable;
use InvalidArgumentException;
use JsonSerializable;
use Override;
use Throwable;

final readonly class Server implements JsonSerializable
{
    public function __construct(
        public string $id,
        public ?int $credentialId,
        public string $name,
        public string $slug,
        public string $type,
        public ?string $ubuntuVersion,
        public int $sshPort,
        public string $provider,
        public ?string $identifier,
        public string $size,
        public string $region,
        public ?string $phpVersion,
        public ?string $phpCliVersion,
        public ?string $opcacheStatus,
        public ?string $databaseType,
        public ?string $dbStatus,
        public ?string $redisStatus,
        public ?string $ipAddress,
        public ?string $privateIpAddress,
        public ?bool $revoked,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt,
        public ?string $connectionStatus,
        public string $timezone,
        public ?string $localPublicKey,
        public bool $isReady,
    ) {}

    /**
     * @param  array<array-key, mixed>  $data  A JSON:API `ServerResource` object.
     */
    public static function from(array $data): self
    {
        $id = $data['id'] ?? null;
        if (! is_string($id)) {
            throw new InvalidArgumentException('Server data is missing the `id` field.');
        }

        $attributes = $data['attributes'] ?? null;
        if (! is_array($attributes)) {
            throw new InvalidArgumentException('Server data is missing the `attributes` object.');
        }

        return new self(
            id: $id,
            credentialId: self::optionalInt($attributes, 'credential_id'),
            name: self::requireString($attributes, 'name'),
            slug: self::requireString($attributes, 'slug'),
            type: self::requireString($attributes, 'type'),
            ubuntuVersion: self::optionalString($attributes, 'ubuntu_version'),
            sshPort: self::requireInt($attributes, 'ssh_port'),
            provider: self::requireString($attributes, 'provider'),
            identifier: self::optionalString($attributes, 'identifier'),
            size: self::requireString($attributes, 'size'),
            region: self::requireString($attributes, 'region'),
            phpVersion: self::optionalString($attributes, 'php_version'),
            phpCliVersion: self::optionalString($attributes, 'php_cli_version'),
            opcacheStatus: self::optionalString($attributes, 'opcache_status'),
            databaseType: self::optionalString($attributes, 'database_type'),
            dbStatus: self::optionalString($attributes, 'db_status'),
            redisStatus: self::optionalString($attributes, 'redis_status'),
            ipAddress: self::optionalString($attributes, 'ip_address'),
            privateIpAddress: self::optionalString($attributes, 'private_ip_address'),
            revoked: self::optionalBool($attributes, 'revoked'),
            createdAt: self::requireDate($attributes, 'created_at'),
            updatedAt: self::requireDate($attributes, 'updated_at'),
            connectionStatus: self::optionalString($attributes, 'connection_status'),
            timezone: self::requireString($attributes, 'timezone'),
            localPublicKey: self::optionalString($attributes, 'local_public_key'),
            isReady: self::requireBool($attributes, 'is_ready'),
        );
    }

    /**
     * @param  array<array-key, mixed>  $attributes
     */
    private static function requireString(array $attributes, string $key): string
    {
        $value = $attributes[$key] ?? null;
        if (! is_string($value)) {
            throw new InvalidArgumentException(sprintf('Server `attributes.%s` must be a string.', $key));
        }

        return $value;
    }

    /**
     * @param  array<array-key, mixed>  $attributes
     */
    private static function optionalString(array $attributes, string $key): ?string
    {
        $value = $attributes[$key] ?? null;
        if ($value === null) {
            return null;
        }

        if (! is_string($value)) {
            throw new InvalidArgumentException(sprintf('Server `attributes.%s` must be a string or null.', $key));
        }

        return $value;
    }

    /**
     * @param  array<array-key, mixed>  $attributes
     */
    private static function requireInt(array $attributes, string $key): int
    {
        $value = $attributes[$key] ?? null;
        if (! is_int($value)) {
            throw new InvalidArgumentException(sprintf('Server `attributes.%s` must be an integer.', $key));
        }

        return $value;
    }

    /**
     * @param  array<array-key, mixed>  $attributes
     */
    private static function optionalInt(array $attributes, string $key): ?int
    {
        $value = $attributes[$key] ?? null;
        if ($value === null) {
            return null;
        }

        if (! is_int($value)) {
            throw new InvalidArgumentException(sprintf('Server `attributes.%s` must be an integer or null.', $key));
        }

        return $value;
    }

    /**
     * @param  array<array-key, mixed>  $attributes
     */
    private static function requireBool(array $attributes, string $key): bool
    {
        $value = $attributes[$key] ?? null;
        if (! is_bool($value)) {
            throw new InvalidArgumentException(sprintf('Server `attributes.%s` must be a boolean.', $key));
        }

        return $value;
    }

    /**
     * @param  array<array-key, mixed>  $attributes
     */
    private static function optionalBool(array $attributes, string $key): ?bool
    {
        $value = $attributes[$key] ?? null;
        if ($value === null) {
            return null;
        }

        if (! is_bool($value)) {
            throw new InvalidArgumentException(sprintf('Server `attributes.%s` must be a boolean or null.', $key));
        }

        return $value;
    }

    /**
     * @param  array<array-key, mixed>  $attributes
     */
    private static function requireDate(array $attributes, string $key): DateTimeImmutable
    {
        $value = $attributes[$key] ?? null;
        if (! is_string($value)) {
            throw new InvalidArgumentException(sprintf('Server `attributes.%s` must be a string.', $key));
        }

        try {
            return new DateTimeImmutable($value);
        } catch (Throwable $throwable) {
            throw new InvalidArgumentException(sprintf('Server `attributes.%s` is not a valid date-time.', $key), 0, $throwable);
        }
    }

    /**
     * @return array{
     *     id: string,
     *     credential_id: ?int,
     *     name: string,
     *     slug: string,
     *     type: string,
     *     ubuntu_version: ?string,
     *     ssh_port: int,
     *     provider: string,
     *     identifier: ?string,
     *     size: string,
     *     region: string,
     *     php_version: ?string,
     *     php_cli_version: ?string,
     *     opcache_status: ?string,
     *     database_type: ?string,
     *     db_status: ?string,
     *     redis_status: ?string,
     *     ip_address: ?string,
     *     private_ip_address: ?string,
     *     revoked: ?bool,
     *     created_at: string,
     *     updated_at: string,
     *     connection_status: ?string,
     *     timezone: string,
     *     local_public_key: ?string,
     *     is_ready: bool,
     * }
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'credential_id' => $this->credentialId,
            'name' => $this->name,
            'slug' => $this->slug,
            'type' => $this->type,
            'ubuntu_version' => $this->ubuntuVersion,
            'ssh_port' => $this->sshPort,
            'provider' => $this->provider,
            'identifier' => $this->identifier,
            'size' => $this->size,
            'region' => $this->region,
            'php_version' => $this->phpVersion,
            'php_cli_version' => $this->phpCliVersion,
            'opcache_status' => $this->opcacheStatus,
            'database_type' => $this->databaseType,
            'db_status' => $this->dbStatus,
            'redis_status' => $this->redisStatus,
            'ip_address' => $this->ipAddress,
            'private_ip_address' => $this->privateIpAddress,
            'revoked' => $this->revoked,
            'created_at' => $this->createdAt->format(DATE_ATOM),
            'updated_at' => $this->updatedAt->format(DATE_ATOM),
            'connection_status' => $this->connectionStatus,
            'timezone' => $this->timezone,
            'local_public_key' => $this->localPublicKey,
            'is_ready' => $this->isReady,
        ];
    }
}
