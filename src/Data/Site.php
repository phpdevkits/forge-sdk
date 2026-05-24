<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

use DateTimeImmutable;
use InvalidArgumentException;
use JsonSerializable;
use Override;
use Throwable;

final readonly class Site implements JsonSerializable
{
    /**
     * @param  list<mixed>  $aliases
     * @param  array<string, string>  $sharedPaths
     */
    public function __construct(
        public string $id,
        public string $name,
        public string $status,
        public string $url,
        public string $user,
        public bool $https,
        public string $webDirectory,
        public ?string $rootDirectory,
        public array $aliases,
        public string $phpVersion,
        public ?string $deploymentStatus,
        public ?bool $quickDeploy,
        public bool $isolated,
        public array $sharedPaths,
        public SiteRepository $repository,
        public ?string $database,
        public SiteMaintenanceMode $maintenanceMode,
        public bool $zeroDowntimeDeployments,
        public ?string $deploymentScript,
        public ?bool $wildcards,
        public ?string $appType,
        public bool $usesEnvoyer,
        public string $deploymentUrl,
        public ?string $healthcheckUrl,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt,
    ) {}

    /**
     * @param  array<array-key, mixed>  $data  A JSON:API `SiteResource` object.
     */
    public static function from(array $data): self
    {
        $id = $data['id'] ?? null;
        if (! is_string($id)) {
            throw new InvalidArgumentException('Site data is missing the `id` field.');
        }

        $attributes = $data['attributes'] ?? null;
        if (! is_array($attributes)) {
            throw new InvalidArgumentException('Site data is missing the `attributes` object.');
        }

        $repository = $attributes['repository'] ?? null;
        if (! is_array($repository)) {
            throw new InvalidArgumentException('Site `attributes.repository` must be an object.');
        }

        $maintenanceMode = $attributes['maintenance_mode'] ?? null;
        if (! is_array($maintenanceMode)) {
            throw new InvalidArgumentException('Site `attributes.maintenance_mode` must be an object.');
        }

        return new self(
            id: $id,
            name: self::requireString($attributes, 'name'),
            status: self::requireString($attributes, 'status'),
            url: self::requireString($attributes, 'url'),
            user: self::requireString($attributes, 'user'),
            https: self::requireBool($attributes, 'https'),
            webDirectory: self::requireString($attributes, 'web_directory'),
            rootDirectory: self::optionalString($attributes, 'root_directory'),
            aliases: self::nullableList($attributes, 'aliases'),
            phpVersion: self::requireString($attributes, 'php_version'),
            deploymentStatus: self::optionalString($attributes, 'deployment_status'),
            quickDeploy: self::optionalBool($attributes, 'quick_deploy'),
            isolated: self::requireBool($attributes, 'isolated'),
            sharedPaths: self::requireStringMap($attributes, 'shared_paths'),
            repository: SiteRepository::from($repository),
            database: self::optionalString($attributes, 'database'),
            maintenanceMode: SiteMaintenanceMode::from($maintenanceMode),
            zeroDowntimeDeployments: self::requireBool($attributes, 'zero_downtime_deployments'),
            deploymentScript: self::optionalString($attributes, 'deployment_script'),
            wildcards: self::optionalBool($attributes, 'wildcards'),
            appType: self::optionalString($attributes, 'app_type'),
            usesEnvoyer: self::requireBool($attributes, 'uses_envoyer'),
            deploymentUrl: self::requireString($attributes, 'deployment_url'),
            healthcheckUrl: self::optionalString($attributes, 'healthcheck_url'),
            createdAt: self::requireDate($attributes, 'created_at'),
            updatedAt: self::requireDate($attributes, 'updated_at'),
        );
    }

    /**
     * @param  array<array-key, mixed>  $attributes
     */
    private static function requireString(array $attributes, string $key): string
    {
        $value = $attributes[$key] ?? null;
        if (! is_string($value)) {
            throw new InvalidArgumentException(sprintf('Site `attributes.%s` must be a string.', $key));
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
            throw new InvalidArgumentException(sprintf('Site `attributes.%s` must be a string or null.', $key));
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
            throw new InvalidArgumentException(sprintf('Site `attributes.%s` must be a boolean.', $key));
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
            throw new InvalidArgumentException(sprintf('Site `attributes.%s` must be a boolean or null.', $key));
        }

        return $value;
    }

    /**
     * Freshly-created sites return `aliases: null` despite the spec marking it
     * a required array — treat null as an empty list.
     *
     * @param  array<array-key, mixed>  $attributes
     * @return list<mixed>
     */
    private static function nullableList(array $attributes, string $key): array
    {
        $value = $attributes[$key] ?? null;
        if ($value === null) {
            return [];
        }

        if (! is_array($value)) {
            throw new InvalidArgumentException(sprintf('Site `attributes.%s` must be an array or null.', $key));
        }

        return array_values($value);
    }

    /**
     * @param  array<array-key, mixed>  $attributes
     * @return array<string, string>
     */
    private static function requireStringMap(array $attributes, string $key): array
    {
        $value = $attributes[$key] ?? null;
        if (! is_array($value)) {
            throw new InvalidArgumentException(sprintf('Site `attributes.%s` must be an object.', $key));
        }

        $map = [];
        foreach ($value as $k => $v) {
            if (is_string($k) && is_string($v)) {
                $map[$k] = $v;
            }
        }

        return $map;
    }

    /**
     * @param  array<array-key, mixed>  $attributes
     */
    private static function requireDate(array $attributes, string $key): DateTimeImmutable
    {
        $value = $attributes[$key] ?? null;
        if (! is_string($value)) {
            throw new InvalidArgumentException(sprintf('Site `attributes.%s` must be a string.', $key));
        }

        try {
            return new DateTimeImmutable($value);
        } catch (Throwable $throwable) {
            throw new InvalidArgumentException(sprintf('Site `attributes.%s` is not a valid date-time.', $key), 0, $throwable);
        }
    }

    /**
     * @return array{
     *     id: string,
     *     name: string,
     *     status: string,
     *     url: string,
     *     user: string,
     *     https: bool,
     *     web_directory: string,
     *     root_directory: ?string,
     *     aliases: list<mixed>,
     *     php_version: string,
     *     deployment_status: ?string,
     *     quick_deploy: ?bool,
     *     isolated: bool,
     *     shared_paths: array<string, string>,
     *     repository: SiteRepository,
     *     database: ?string,
     *     maintenance_mode: SiteMaintenanceMode,
     *     zero_downtime_deployments: bool,
     *     deployment_script: ?string,
     *     wildcards: ?bool,
     *     app_type: ?string,
     *     uses_envoyer: bool,
     *     deployment_url: string,
     *     healthcheck_url: ?string,
     *     created_at: string,
     *     updated_at: string,
     * }
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'url' => $this->url,
            'user' => $this->user,
            'https' => $this->https,
            'web_directory' => $this->webDirectory,
            'root_directory' => $this->rootDirectory,
            'aliases' => $this->aliases,
            'php_version' => $this->phpVersion,
            'deployment_status' => $this->deploymentStatus,
            'quick_deploy' => $this->quickDeploy,
            'isolated' => $this->isolated,
            'shared_paths' => $this->sharedPaths,
            'repository' => $this->repository,
            'database' => $this->database,
            'maintenance_mode' => $this->maintenanceMode,
            'zero_downtime_deployments' => $this->zeroDowntimeDeployments,
            'deployment_script' => $this->deploymentScript,
            'wildcards' => $this->wildcards,
            'app_type' => $this->appType,
            'uses_envoyer' => $this->usesEnvoyer,
            'deployment_url' => $this->deploymentUrl,
            'healthcheck_url' => $this->healthcheckUrl,
            'created_at' => $this->createdAt->format(DATE_ATOM),
            'updated_at' => $this->updatedAt->format(DATE_ATOM),
        ];
    }
}
