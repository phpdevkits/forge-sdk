<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

/**
 * Input payload for
 * POST /orgs/{org}/server-credentials/{credential}/regions/{region}/vpcs.
 */
final readonly class CreateVpcData
{
    public function __construct(
        public string $name,
    ) {}

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}
