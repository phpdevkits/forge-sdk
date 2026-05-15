<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use PhpDevKits\ForgeSdk\Enums\Provider;

test('Provider exposes the canonical Forge compute provider slugs',
    function (): void {
        expect(Provider::DigitalOcean->value)->toBe('digitalocean')
            ->and(Provider::Linode->value)->toBe('linode')
            ->and(Provider::Akamai->value)->toBe('akamai')
            ->and(Provider::Vultr->value)->toBe('vultr')
            ->and(Provider::Aws->value)->toBe('aws')
            ->and(Provider::Hetzner->value)->toBe('hetzner')
            ->and(Provider::Custom->value)->toBe('custom');
    });

test('Provider::tryFrom returns null for unknown slugs',
    function (): void {
        expect(Provider::tryFrom('cloudflare'))->toBeNull();
    });
