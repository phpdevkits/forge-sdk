<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use PhpDevKits\ForgeSdk\Data\CreateDaemonData;
use PhpDevKits\ForgeSdk\Enums\DaemonUser;
use Tests\Factories\CreateDaemonDataFactory;

beforeEach(function (): void {
    $this->factory = new CreateDaemonDataFactory;
});

test('factory produces a usable CreateDaemonData',
    function (): void {
        expect($this->factory->make())->toBeInstanceOf(CreateDaemonData::class);
    });

test('toArray() emits the required fields with the user enum serialized',
    function (): void {
        $data = new CreateDaemonData(name: 'worker', command: 'sleep 9999', user: DaemonUser::Forge);

        expect($data->toArray())->toBe([
            'name' => 'worker',
            'command' => 'sleep 9999',
            'user' => 'forge',
            'processes' => 1,
        ]);
    });

test('toArray() emits optional fields when set',
    function (): void {
        $data = new CreateDaemonData(
            name: 'worker',
            command: 'sleep 9999',
            user: DaemonUser::Root,
            processes: 3,
            siteId: 12345,
            directory: '/home/forge/example.com',
            startSecs: 5,
            stopWaitSecs: 10,
            stopSignal: 'SIGTERM',
        );

        expect($data->toArray())->toBe([
            'name' => 'worker',
            'command' => 'sleep 9999',
            'user' => 'root',
            'processes' => 3,
            'site_id' => 12345,
            'directory' => '/home/forge/example.com',
            'startsecs' => 5,
            'stopwaitsecs' => 10,
            'stopsignal' => 'SIGTERM',
        ]);
    });
