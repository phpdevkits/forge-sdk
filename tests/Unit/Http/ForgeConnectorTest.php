<?php

declare(strict_types=1);

namespace Tests\Unit\Http;

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
use PhpDevKits\ForgeSdk\Requests\Me\GetMeRequest;
use RuntimeException;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\PendingRequest;
use Tests\Utils\ForgeFixture;

test('throws BadRequestException on a 400 response',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetMeRequest::class => new ForgeFixture('errors/me-400'),
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
            GetMeRequest::class => new ForgeFixture('errors/me-401'),
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
            GetMeRequest::class => new ForgeFixture('errors/me-403'),
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
            GetMeRequest::class => new ForgeFixture('errors/me-404'),
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
            GetMeRequest::class => new ForgeFixture('errors/me-422'),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        $forge->me();
    })->throws(ValidationException::class);

test('ValidationException exposes the parsed Laravel error bag',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetMeRequest::class => new ForgeFixture('errors/me-422'),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        expect(fn (): User => $forge->me())->toThrow(function (ValidationException $validationException): void {
            expect($validationException->errors())->toHaveKey('name')
                ->and($validationException->errors())->toHaveKey('email')
                ->and($validationException->errorsFor('name'))->toBe([
                    'The name field is required.',
                    'The name must be at least 3 characters.',
                ])
                ->and($validationException->firstError('name'))->toBe('The name field is required.')
                ->and($validationException->firstError('nonexistent'))->toBeNull();
        });
    });

test('ValidationException::errors() returns an empty array when no errors key is present',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetMeRequest::class => new ForgeFixture('errors/me-422-empty'),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        expect(fn (): User => $forge->me())->toThrow(function (ValidationException $validationException): void {
            expect($validationException->errors())->toBe([])
                ->and($validationException->errorsFor('anything'))->toBe([])
                ->and($validationException->firstError('anything'))->toBeNull();
        });
    });

test('ValidationException::errors() skips entries with non-string keys or non-array messages',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetMeRequest::class => new ForgeFixture('errors/me-422-bad-shape'),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        expect(fn (): User => $forge->me())->toThrow(function (ValidationException $validationException): void {
            expect($validationException->errors())->toBe([]);
        });
    });

test('throws RateLimitException on a 429 response',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetMeRequest::class => new ForgeFixture('errors/me-429'),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        $forge->me();
    })->throws(RateLimitException::class);

test('RateLimitException parses the Retry-After header',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetMeRequest::class => new ForgeFixture('errors/me-429'),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        expect(fn (): User => $forge->me())->toThrow(function (RateLimitException $rateLimitException): void {
            expect($rateLimitException->retryAfter())->toBe(60);
        });
    });

test('RateLimitException::retryAfter() returns null when the header is missing',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetMeRequest::class => new ForgeFixture('errors/me-429-no-header'),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        expect(fn (): User => $forge->me())->toThrow(function (RateLimitException $rateLimitException): void {
            expect($rateLimitException->retryAfter())->toBeNull();
        });
    });

test('throws ServerException on a 500 response',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetMeRequest::class => new ForgeFixture('errors/me-500'),
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
            GetMeRequest::class => new ForgeFixture('errors/me-503'),
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
            GetMeRequest::class => new ForgeFixture('errors/me-418'),
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
            GetMeRequest::class => new ForgeFixture('errors/me-404'),
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
            GetMeRequest::class => MockResponse::make()->throw(
                fn (PendingRequest $pendingRequest): FatalRequestException => new FatalRequestException(
                    new RuntimeException('Connection refused'),
                    $pendingRequest,
                ),
            ),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        $forge->me();
    })->throws(ConnectionException::class, 'Forge API connection failed');
