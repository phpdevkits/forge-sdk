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

test('rejects malformed User data',
    /**
     * @param  array<array-key, mixed>  $data
     *
     * @throws InvalidArgumentException
     */
    function (array $data): void {
        expect(fn (): User => User::from($data))->toThrow(InvalidArgumentException::class);
    })->with([
        'missing id' => [['attributes' => ['name' => 'X', 'email' => 'x@example.com']]],
        'non-string id' => [['id' => 42, 'attributes' => ['name' => 'X', 'email' => 'x@example.com']]],
        'missing attributes' => [['id' => '1']],
        'non-array attributes' => [['id' => '1', 'attributes' => 'nope']],
        'missing name' => [['id' => '1', 'attributes' => ['email' => 'x@example.com']]],
        'missing email' => [['id' => '1', 'attributes' => ['name' => 'X']]],
    ]);
