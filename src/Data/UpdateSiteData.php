<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

use PhpDevKits\ForgeSdk\Enums\PhpVersion;
use PhpDevKits\ForgeSdk\Enums\SiteType;

/**
 * Input payload for PUT /orgs/{org}/servers/{server}/sites/{site}. All
 * fields optional; `toArray()` strips nulls so partial updates only send
 * what changed.
 */
final readonly class UpdateSiteData
{
    public function __construct(
        public ?string $rootPath = null,
        public ?string $directory = null,
        public ?SiteType $type = null,
        public ?PhpVersion $phpVersion = null,
        public ?bool $pushToDeploy = null,
        public ?string $repositoryBranch = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $payload = [];

        if ($this->rootPath !== null) {
            $payload['root_path'] = $this->rootPath;
        }

        if ($this->directory !== null) {
            $payload['directory'] = $this->directory;
        }

        if ($this->type instanceof SiteType) {
            $payload['type'] = $this->type->value;
        }

        if ($this->phpVersion instanceof PhpVersion) {
            $payload['php_version'] = $this->phpVersion->value;
        }

        if ($this->pushToDeploy !== null) {
            $payload['push_to_deploy'] = $this->pushToDeploy;
        }

        if ($this->repositoryBranch !== null) {
            $payload['repository_branch'] = $this->repositoryBranch;
        }

        return $payload;
    }
}
