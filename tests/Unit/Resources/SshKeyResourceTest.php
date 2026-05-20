<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Data\SshKey;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Servers\GetSshKey;
use Saloon\Http\Faking\MockClient;
use Tests\Utils\ServerFixture;

test('get() returns a hydrated SshKey',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $serverId = ($_ENV['FORGE_TEST_SERVER_ID'] ?? '') ?: '1';
        $keyId = ($_ENV['FORGE_TEST_SSH_KEY_ID'] ?? '') ?: '1';
        $mockClient = new MockClient([
            GetSshKey::class => new ServerFixture('ssh-keys/get'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);

        $key = $forge->server($serverId)->sshKey($keyId)->get();

        expect($key)->toBeInstanceOf(SshKey::class)
            ->and($key->id)->toBeString()->not->toBeEmpty()
            ->and($key->name)->toBeString()->not->toBeEmpty();
    });
