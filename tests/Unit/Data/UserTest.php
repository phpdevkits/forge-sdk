<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use InvalidArgumentException;
use PhpDevKits\ForgeSdk\Data\User;
use Tests\Factories\UserFactory;

beforeEach(function (): void {
    $this->userFactory = new UserFactory;
});

test('factory produces a usable User',
    function (): void {
        $user = $this->userFactory->make();

        expect($user)->toBeInstanceOf(User::class)
            ->and($user->id)->toBeString()->not->toBeEmpty()
            ->and($user->name)->toBeString()->not->toBeEmpty()
            ->and($user->email)->toBeString()->toContain('@');
    });

test('hydrates from a JSON:API data envelope',
    /**
     * @throws InvalidArgumentException
     */
    function (): void {
        $user = User::from([
            'id' => '42',
            'type' => 'users',
            'attributes' => [
                'name' => 'Ada Lovelace',
                'email' => 'ada@example.com',
            ],
            'links' => ['self' => ['href' => 'https://forge.laravel.com/api/user']],
        ]);

        expect($user->id)->toBe('42')
            ->and($user->name)->toBe('Ada Lovelace')
            ->and($user->email)->toBe('ada@example.com');
    });

test('User::from() throws when id is missing',
    /**
     * @throws InvalidArgumentException
     */
    function (): void {
        expect(fn (): User => User::from(['attributes' => ['name' => 'X', 'email' => 'x@example.com']]))
            ->toThrow(InvalidArgumentException::class, 'User: `id` must be a string.');
    });

test('User::from() throws when id is not a string',
    /**
     * @throws InvalidArgumentException
     */
    function (): void {
        expect(fn (): User => User::from(['id' => 42, 'attributes' => ['name' => 'X', 'email' => 'x@example.com']]))
            ->toThrow(InvalidArgumentException::class, 'User: `id` must be a string.');
    });

test('User::from() throws when attributes is missing',
    /**
     * @throws InvalidArgumentException
     */
    function (): void {
        expect(fn (): User => User::from(['id' => '1']))
            ->toThrow(InvalidArgumentException::class, 'User: `attributes` must be an object.');
    });

test('User::from() throws when attributes is not an array',
    /**
     * @throws InvalidArgumentException
     */
    function (): void {
        expect(fn (): User => User::from(['id' => '1', 'attributes' => 'nope']))
            ->toThrow(InvalidArgumentException::class, 'User: `attributes` must be an object.');
    });

test('User::from() throws when name is missing',
    /**
     * @throws InvalidArgumentException
     */
    function (): void {
        expect(fn (): User => User::from(['id' => '1', 'attributes' => ['email' => 'x@example.com']]))
            ->toThrow(InvalidArgumentException::class, 'User: `attributes.name` must be a string.');
    });

test('User::from() throws when email is missing',
    /**
     * @throws InvalidArgumentException
     */
    function (): void {
        expect(fn (): User => User::from(['id' => '1', 'attributes' => ['name' => 'X']]))
            ->toThrow(InvalidArgumentException::class, 'User: `attributes.email` must be a string.');
    });
