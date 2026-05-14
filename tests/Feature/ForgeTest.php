<?php

declare(strict_types=1);

namespace Tests\Feature;

use PhpDevKits\ForgeSdk\Data\User;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Me\GetMeRequest;
use RuntimeException;
use Saloon\Http\Faking\MockClient;
use Tests\Utils\ForgeFixture;

beforeEach(function (): void {
    $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';

    $this->mockClient = new MockClient([
        GetMeRequest::class => new ForgeFixture('users/me'),
    ]);

    $this->forge = new Forge($token)->withMockClient($this->mockClient);
});

test('returns the authenticated user from /me',
    /**
     * @throws Throwable
     */
    function (): void {
        $user = $this->forge->me();

        expect($user)->toBeInstanceOf(User::class)
            ->and($user->id)->toBeString()->not->toBeEmpty()
            ->and($user->name)->toBeString()->not->toBeEmpty()
            ->and($user->email)->toBeString()->toContain('@');
    });

test('sends the /me request to the forge.laravel.com/api base URL',
    /**
     * @throws Throwable
     */
    function (): void {
        $this->forge->me();

        $url = $this->mockClient->getLastPendingRequest()?->getUrl() ?? '';

        expect($url)->toBe('https://forge.laravel.com/api/me');
    });

test('sends the bearer token in the Authorization header',
    /**
     * @throws Throwable
     */
    function (): void {
        $forge = new Forge('secret-pat-123')->withMockClient($this->mockClient);

        $forge->me();

        $headers = $this->mockClient->getLastPendingRequest()?->headers();

        expect($headers?->get('Authorization'))->toBe('Bearer secret-pat-123');
    });

test('negotiates JSON:API content type',
    /**
     * @throws Throwable
     */
    function (): void {
        $this->forge->me();

        $headers = $this->mockClient->getLastPendingRequest()?->headers();

        expect($headers?->get('Accept'))->toBe('application/vnd.api+json')
            ->and($headers?->get('Content-Type'))->toBe('application/vnd.api+json');
    });

test('throws when the /me response has no data object',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetMeRequest::class => new ForgeFixture('users/me-missing-data'),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        expect(fn (): User => $forge->me())
            ->toThrow(RuntimeException::class, 'Forge /me response did not include a `data` object.');
    });
