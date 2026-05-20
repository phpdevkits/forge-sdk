<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use PhpDevKits\ForgeSdk\Data\CreateSshKeyData;
use Tests\Factories\CreateSshKeyDataFactory;

beforeEach(function (): void {
    $this->factory = new CreateSshKeyDataFactory;
});

test('factory produces a usable CreateSshKeyData',
    function (): void {
        $data = $this->factory->make();

        expect($data)->toBeInstanceOf(CreateSshKeyData::class)
            ->and($data->name)->toBeString()->not->toBeEmpty()
            ->and($data->key)->toContain('ssh-ed25519');
    });

test('toArray() emits only required fields when user is null',
    function (): void {
        $data = new CreateSshKeyData(name: 'oliver-macbook', key: 'ssh-ed25519 AAAA...');

        expect($data->toArray())->toBe([
            'name' => 'oliver-macbook',
            'key' => 'ssh-ed25519 AAAA...',
        ]);
    });

test('toArray() emits user when set',
    function (): void {
        $data = new CreateSshKeyData(name: 'x', key: 'ssh-ed25519 ...', user: 'deploy');

        expect($data->toArray())->toBe([
            'name' => 'x',
            'key' => 'ssh-ed25519 ...',
            'user' => 'deploy',
        ]);
    });
