<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

use PhpDevKits\ForgeSdk\Enums\RuleType;

/**
 * Input payload for POST /orgs/{org}/servers/{server}/firewall-rules.
 *
 * `port` and `ip_address` are optional; omitting `ip_address` applies the
 * rule to all addresses.
 */
final readonly class CreateFirewallRuleData
{
    public function __construct(
        public string $name,
        public RuleType $type,
        public ?string $port = null,
        public ?string $ipAddress = null,
    ) {}

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        $payload = [
            'name' => $this->name,
            'type' => $this->type->value,
        ];

        if ($this->port !== null) {
            $payload['port'] = $this->port;
        }

        if ($this->ipAddress !== null) {
            $payload['ip_address'] = $this->ipAddress;
        }

        return $payload;
    }
}
