<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use PhpDevKits\ForgeSdk\Data\UpdateEnvironmentData;
use Tests\Factories\UpdateEnvironmentDataFactory;

beforeEach(function (): void {
    $this->factory = new UpdateEnvironmentDataFactory;
});

test('factory produces a usable UpdateEnvironmentData',
    function (): void {
        expect($this->factory->make())->toBeInstanceOf(UpdateEnvironmentData::class);
    });

test('toArray() emits only environment when optionals are null',
    function (): void {
        expect(new UpdateEnvironmentData(content: 'APP_ENV=production')->toArray())->toBe([
            'environment' => 'APP_ENV=production',
        ]);
    });

test('toArray() emits cache when set',
    function (): void {
        expect(new UpdateEnvironmentData(content: 'APP_ENV=production', cache: true)->toArray())->toBe([
            'environment' => 'APP_ENV=production',
            'cache' => true,
        ]);
    });

test('toArray() emits queues when set',
    function (): void {
        expect(new UpdateEnvironmentData(content: 'APP_ENV=production', queues: false)->toArray())->toBe([
            'environment' => 'APP_ENV=production',
            'queues' => false,
        ]);
    });

test('toArray() emits encryption_key when set',
    function (): void {
        expect(new UpdateEnvironmentData(content: 'APP_ENV=production', encryptionKey: 'base64:abc')->toArray())->toBe([
            'environment' => 'APP_ENV=production',
            'encryption_key' => 'base64:abc',
        ]);
    });

test('toArray() emits all optionals together when set',
    function (): void {
        expect(new UpdateEnvironmentData(
            content: 'APP_ENV=production',
            cache: true,
            queues: true,
            encryptionKey: 'base64:abc',
        )->toArray())->toBe([
            'environment' => 'APP_ENV=production',
            'cache' => true,
            'queues' => true,
            'encryption_key' => 'base64:abc',
        ]);
    });
