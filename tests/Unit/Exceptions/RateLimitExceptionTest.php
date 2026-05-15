<?php

declare(strict_types=1);

namespace Tests\Unit\Exceptions;

use PhpDevKits\ForgeSdk\Data\User;
use PhpDevKits\ForgeSdk\Exceptions\RateLimitException;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Me\GetMe;
use Saloon\Http\Faking\MockClient;
use Tests\Utils\ForgeFixture;

test('retryAfter() parses the Retry-After header',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetMe::class => new ForgeFixture('me/me-429'),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        expect(fn (): User => $forge->me())->toThrow(function (RateLimitException $rateLimitException): void {
            expect($rateLimitException->retryAfter())->toBe(60);
        });
    });

test('retryAfter() returns null when the header is missing',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetMe::class => new ForgeFixture('me/me-429-no-header'),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        expect(fn (): User => $forge->me())->toThrow(function (RateLimitException $rateLimitException): void {
            expect($rateLimitException->retryAfter())->toBeNull();
        });
    });
