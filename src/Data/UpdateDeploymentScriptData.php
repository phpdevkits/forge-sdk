<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

/**
 * Input payload for PUT /orgs/{org}/servers/{server}/sites/{site}/deployments/script.
 */
final readonly class UpdateDeploymentScriptData
{
    public function __construct(
        public string $content,
        public ?bool $autoSource = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $payload = ['content' => $this->content];

        if ($this->autoSource !== null) {
            $payload['auto_source'] = $this->autoSource;
        }

        return $payload;
    }
}
