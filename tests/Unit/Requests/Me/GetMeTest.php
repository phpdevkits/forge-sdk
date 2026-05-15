<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Me;

use PhpDevKits\ForgeSdk\Data\User;
use PhpDevKits\ForgeSdk\Exceptions\ApiException;
use PhpDevKits\ForgeSdk\Exceptions\BadRequestException;
use PhpDevKits\ForgeSdk\Exceptions\ConnectionException;
use PhpDevKits\ForgeSdk\Exceptions\ForbiddenException;
use PhpDevKits\ForgeSdk\Exceptions\NotFoundException;
use PhpDevKits\ForgeSdk\Exceptions\RateLimitException;
use PhpDevKits\ForgeSdk\Exceptions\ServerException;
use PhpDevKits\ForgeSdk\Exceptions\UnauthorizedException;
use PhpDevKits\ForgeSdk\Exceptions\ValidationException;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Me\GetMe;
use RuntimeException;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\PendingRequest;
use Tests\Utils\ForgeFixture;

beforeEach(function (): void {
    $this->mockClient = new MockClient([
        GetMe::class => new ForgeFixture('me/me'),
    ]);

    $this->forge = new Forge('test-token')->withMockClient($this->mockClient);
});

test('returns the authenticated user',
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

test('sends to the forge.laravel.com/api base URL',
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

test('throws when the response has no data object',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetMe::class => new ForgeFixture('me/me-missing-data'),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        $forge->me();
    })->throws(RuntimeException::class, 'Forge /me response did not include a `data` object.');

test('throws BadRequestException on a 400 response',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetMe::class => new ForgeFixture('me/me-400'),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        $forge->me();
    })->throws(BadRequestException::class);

test('throws UnauthorizedException on a 401 response',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetMe::class => new ForgeFixture('me/me-401'),
        ]);
        $forge = new Forge('bad-token')->withMockClient($mockClient);

        $forge->me();
    })->throws(UnauthorizedException::class);

test('throws ForbiddenException on a 403 response',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetMe::class => new ForgeFixture('me/me-403'),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        $forge->me();
    })->throws(ForbiddenException::class);

test('throws NotFoundException on a 404 response',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetMe::class => new ForgeFixture('me/me-404'),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        $forge->me();
    })->throws(NotFoundException::class);

test('throws ValidationException on a 422 response',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetMe::class => new ForgeFixture('me/me-422'),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        $forge->me();
    })->throws(ValidationException::class);

test('throws RateLimitException on a 429 response',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetMe::class => new ForgeFixture('me/me-429'),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        $forge->me();
    })->throws(RateLimitException::class);

test('throws ServerException on a 500 response',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetMe::class => new ForgeFixture('me/me-500'),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        $forge->me();
    })->throws(ServerException::class);

test('throws ServerException on any 5xx response',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetMe::class => new ForgeFixture('me/me-503'),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        $forge->me();
    })->throws(ServerException::class);

test('falls back to ApiException for an unknown status code',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetMe::class => new ForgeFixture('me/me-418'),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        $forge->me();
    })->throws(ApiException::class);

test('ApiException::status() returns the response status code',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetMe::class => new ForgeFixture('me/me-404'),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        expect(fn (): User => $forge->me())->toThrow(function (NotFoundException $notFoundException): void {
            expect($notFoundException->status())->toBe(404);
        });
    });

test('wraps Saloon transport errors in ConnectionException',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetMe::class => MockResponse::make()->throw(
                fn (PendingRequest $pendingRequest): FatalRequestException => new FatalRequestException(
                    new RuntimeException('Connection refused'),
                    $pendingRequest,
                ),
            ),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        $forge->me();
    })->throws(ConnectionException::class, 'Forge API connection failed');
