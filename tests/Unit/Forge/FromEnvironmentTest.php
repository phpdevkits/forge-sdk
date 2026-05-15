<?php

declare(strict_types=1);

namespace Tests\Unit\Forge;

use InvalidArgumentException;
use PhpDevKits\ForgeSdk\Forge;

afterEach(function (): void {
    unset($_ENV['FORGE_TOKEN'], $_ENV['FORGE_ORGANIZATION']);
});

test('Forge::fromEnvironment() reads the token from FORGE_TOKEN',
    function (): void {
        $_ENV['FORGE_TOKEN'] = 'env-token-abc';

        $forge = Forge::fromEnvironment();

        expect($forge)->toBeInstanceOf(Forge::class);
    });

test('Forge::fromEnvironment() captures FORGE_ORGANIZATION when set',
    function (): void {
        $_ENV['FORGE_TOKEN'] = 'env-token-abc';
        $_ENV['FORGE_ORGANIZATION'] = 'acme';

        $forge = Forge::fromEnvironment();

        expect($forge->defaultOrganization)->toBe('acme');
    });

test('Forge::fromEnvironment() leaves defaultOrganization null when FORGE_ORGANIZATION is unset',
    function (): void {
        $_ENV['FORGE_TOKEN'] = 'env-token-abc';

        $forge = Forge::fromEnvironment();

        expect($forge->defaultOrganization)->toBeNull();
    });

test('Forge::fromEnvironment() throws when FORGE_TOKEN is missing',
    function (): void {
        unset($_ENV['FORGE_TOKEN']);

        Forge::fromEnvironment();
    })->throws(InvalidArgumentException::class, 'FORGE_TOKEN environment variable is required.');

test('Forge::fromEnvironment() throws when FORGE_TOKEN is an empty string',
    function (): void {
        $_ENV['FORGE_TOKEN'] = '';

        Forge::fromEnvironment();
    })->throws(InvalidArgumentException::class, 'FORGE_TOKEN environment variable is required.');
