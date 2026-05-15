<?php

declare(strict_types=1);

namespace Tests\Unit\Exceptions;

use PhpDevKits\ForgeSdk\Data\User;
use PhpDevKits\ForgeSdk\Exceptions\ValidationException;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Me\GetMe;
use Saloon\Http\Faking\MockClient;
use Tests\Utils\ForgeFixture;

test('exposes the parsed Laravel error bag',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetMe::class => new ForgeFixture('me/me-422'),
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

test('errors() returns an empty array when no errors key is present',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetMe::class => new ForgeFixture('me/me-422-empty'),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        expect(fn (): User => $forge->me())->toThrow(function (ValidationException $validationException): void {
            expect($validationException->errors())->toBe([])
                ->and($validationException->errorsFor('anything'))->toBe([])
                ->and($validationException->firstError('anything'))->toBeNull();
        });
    });

test('errors() skips entries with non-string keys or non-array messages',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetMe::class => new ForgeFixture('me/me-422-bad-shape'),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        expect(fn (): User => $forge->me())->toThrow(function (ValidationException $validationException): void {
            expect($validationException->errors())->toBe([]);
        });
    });
