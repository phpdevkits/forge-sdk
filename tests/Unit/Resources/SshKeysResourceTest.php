<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Data\Page;
use PhpDevKits\ForgeSdk\Data\SshKey;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Servers\GetSshKeys;
use Saloon\Http\Faking\MockClient;
use Tests\Utils\ServerFixture;

test('all() returns a Page<SshKey>',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $serverId = ($_ENV['FORGE_TEST_SERVER_ID'] ?? '') ?: '1';
        $mockClient = new MockClient([
            GetSshKeys::class => new ServerFixture('ssh-keys/list'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);

        $page = $forge->server($serverId)->sshKeys()->all();

        expect($page)->toBeInstanceOf(Page::class)
            ->and($page->data)->not->toBeEmpty()
            ->and($page->data[0])->toBeInstanceOf(SshKey::class);
    });

test('iterate() yields each key returned by the API',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $serverId = ($_ENV['FORGE_TEST_SERVER_ID'] ?? '') ?: '1';
        $mockClient = new MockClient([
            GetSshKeys::class => new ServerFixture('ssh-keys/list'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);

        $keys = iterator_to_array($forge->server($serverId)->sshKeys()->iterate());

        expect($keys)->not->toBeEmpty()
            ->and($keys[0])->toBeInstanceOf(SshKey::class);
    });
