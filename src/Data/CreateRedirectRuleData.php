<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

use PhpDevKits\ForgeSdk\Enums\RedirectType;

/**
 * Input payload for POST /orgs/{org}/servers/{server}/sites/{site}/redirect-rules.
 *
 * All three fields are required by the API.
 */
final readonly class CreateRedirectRuleData
{
    public function __construct(
        public string $from,
        public string $to,
        public RedirectType $type,
    ) {}

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            'from' => $this->from,
            'to' => $this->to,
            'type' => $this->type->value,
        ];
    }
}
