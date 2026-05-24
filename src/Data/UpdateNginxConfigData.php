<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

/**
 * Input payload for PUT /orgs/{org}/servers/{server}/sites/{site}/nginx.
 */
final readonly class UpdateNginxConfigData
{
    public function __construct(
        public string $content,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return ['config' => $this->content];
    }
}
