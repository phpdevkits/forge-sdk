<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

use PhpDevKits\ForgeSdk\Enums\PhpVersion;
use PhpDevKits\ForgeSdk\Enums\SiteType;
use PhpDevKits\ForgeSdk\Enums\WwwRedirectType;

/**
 * Input payload for POST /orgs/{org}/servers/{server}/sites.
 *
 * Only `type` is required. The remaining fields cover the common
 * create surface — Statamic-specific and deploy-key fields are omitted
 * until the SDK needs them.
 */
final readonly class CreateSiteData
{
    public function __construct(
        public SiteType $type,
        public ?string $name = null,
        public ?string $domainMode = null,
        public ?WwwRedirectType $wwwRedirectType = null,
        public ?string $rootDirectory = null,
        public ?string $webDirectory = null,
        public ?bool $isIsolated = null,
        public ?string $isolatedUser = null,
        public ?PhpVersion $phpVersion = null,
        public ?bool $zeroDowntimeDeployments = null,
        public ?string $sourceControlProvider = null,
        public ?string $repository = null,
        public ?string $branch = null,
        public ?int $databaseId = null,
        public ?int $databaseUserId = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $payload = ['type' => $this->type->value];

        if ($this->name !== null) {
            $payload['name'] = $this->name;
        }

        if ($this->domainMode !== null) {
            $payload['domain_mode'] = $this->domainMode;
        }

        if ($this->wwwRedirectType instanceof WwwRedirectType) {
            $payload['www_redirect_type'] = $this->wwwRedirectType->value;
        }

        if ($this->rootDirectory !== null) {
            $payload['root_directory'] = $this->rootDirectory;
        }

        if ($this->webDirectory !== null) {
            $payload['web_directory'] = $this->webDirectory;
        }

        if ($this->isIsolated !== null) {
            $payload['is_isolated'] = $this->isIsolated;
        }

        if ($this->isolatedUser !== null) {
            $payload['isolated_user'] = $this->isolatedUser;
        }

        if ($this->phpVersion instanceof PhpVersion) {
            $payload['php_version'] = $this->phpVersion->value;
        }

        if ($this->zeroDowntimeDeployments !== null) {
            $payload['zero_downtime_deployments'] = $this->zeroDowntimeDeployments;
        }

        if ($this->sourceControlProvider !== null) {
            $payload['source_control_provider'] = $this->sourceControlProvider;
        }

        if ($this->repository !== null) {
            $payload['repository'] = $this->repository;
        }

        if ($this->branch !== null) {
            $payload['branch'] = $this->branch;
        }

        if ($this->databaseId !== null) {
            $payload['database_id'] = $this->databaseId;
        }

        if ($this->databaseUserId !== null) {
            $payload['database_user_id'] = $this->databaseUserId;
        }

        return $payload;
    }
}
