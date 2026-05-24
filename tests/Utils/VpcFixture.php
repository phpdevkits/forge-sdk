<?php

declare(strict_types=1);

namespace Tests\Utils;

use Override;

/**
 * Saloon fixture for the VPC endpoints. Inherits the shared redactions in
 * {@see ForgeFixture} and additionally scrubs network topology — CIDR blocks
 * and the provider region — which would otherwise reveal the account's real
 * private network layout. Pagination cursors are stabilized too.
 */
final class VpcFixture extends ForgeFixture
{
    /**
     * @return array<string, string|int|callable>
     */
    #[Override]
    protected function defineSensitiveJsonParameters(): array
    {
        $stableCursor = static fn (mixed $value): ?string => $value === null ? null : 'CURSOR-A';

        return [
            ...parent::defineSensitiveJsonParameters(),
            'next_cursor' => $stableCursor,
            'prev_cursor' => $stableCursor,
            'cidrBlock' => '10.0.0.0/16',
            'cidr_block' => '10.0.0.0/16',
            'cidr' => '10.0.0.0/24',
            'region' => 'test-region',
        ];
    }
}
