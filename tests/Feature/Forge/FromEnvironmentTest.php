<?php

declare(strict_types=1);

namespace Tests\Feature\Forge;

use InvalidArgumentException;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Me\GetMeRequest;
use Saloon\Http\Faking\MockClient;
use Tests\Utils\ForgeFixture;

afterEach(function (): void {
    unset(
        $_ENV['FORGE_TOKEN'],
        $_ENV['FORGE_ORGANIZATION'],
    );
});

test('Forge::fromEnvironment() reads the token from FORGE_TOKEN',
    /**
     * @throws Throwable
     */
    function (): void {
        $_ENV['FORGE_TOKEN'] = 'env-token-abc';

        $forge = Forge::fromEnvironment();

        $mockClient = new MockClient([
            GetMeRequest::class => new ForgeFixture('users/me'),
        ]);
        $forge->withMockClient($mockClient);

        $forge->me();

        $headers = $mockClient->getLastPendingRequest()?->headers();

        expect($headers?->get('Authorization'))->toBe('Bearer env-token-abc');
    });

test('Forge::fromEnvironment() captures FORGE_ORGANIZATION when set',
    /**
     * @throws Throwable
     */
    function (): void {
        $_ENV['FORGE_TOKEN'] = 'env-token-abc';
        $_ENV['FORGE_ORGANIZATION'] = 'acme';

        $forge = Forge::fromEnvironment();

        expect($forge->organization)->toBe('acme');
    });

test('Forge::fromEnvironment() leaves organization null when FORGE_ORGANIZATION is unset',
    /**
     * @throws Throwable
     */
    function (): void {
        $_ENV['FORGE_TOKEN'] = 'env-token-abc';

        $forge = Forge::fromEnvironment();

        expect($forge->organization)->toBeNull();
    });

test('Forge::fromEnvironment() throws when FORGE_TOKEN is missing',
    /**
     * @throws InvalidArgumentException
     */
    function (): void {
        unset($_ENV['FORGE_TOKEN']);

        Forge::fromEnvironment();
    })->throws(InvalidArgumentException::class, 'FORGE_TOKEN environment variable is required.');

test('Forge::fromEnvironment() throws when FORGE_TOKEN is an empty string',
    /**
     * @throws InvalidArgumentException
     */
    function (): void {
        $_ENV['FORGE_TOKEN'] = '';

        Forge::fromEnvironment();
    })->throws(InvalidArgumentException::class, 'FORGE_TOKEN environment variable is required.');
