<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Data\CreateSshKeyData;
use PhpDevKits\ForgeSdk\Data\SshKey;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Servers\CreateSshKey;
use PhpDevKits\ForgeSdk\Requests\Servers\DeleteSshKey;
use PhpDevKits\ForgeSdk\Requests\Servers\GetSshKey;
use PhpDevKits\ForgeSdk\Requests\Servers\GetSshKeys;
use Saloon\Http\Faking\MockClient;
use Tests\Utils\ServerFixture;
use Throwable;

/*
 * End-to-end SSH key lifecycle exercised against the configured Forge
 * server (recording mode) or replayed from committed fixtures otherwise.
 *
 * Order: create -> list -> get -> delete, all on the same key, so the
 * list and get fixtures contain meaningful data (rather than an empty
 * page from a key-less server). try/finally guarantees the throwaway
 * key is cleaned up if any step fails.
 *
 * The `key` blob is a real, validly-formatted ed25519 public key (the
 * matching private key was generated and discarded immediately — it has
 * never been deployed anywhere and is not trusted by any host). It's
 * hardcoded so fixture recordings are reproducible.
 *
 * Required env vars (recording only): FORGE_TEST_TOKEN, FORGE_TEST_ORG_SLUG,
 * FORGE_TEST_SERVER_ID.
 */
test('lifecycle: create -> list -> get -> delete',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $serverId = ($_ENV['FORGE_TEST_SERVER_ID'] ?? '') ?: '1';

        $publicKey = 'ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIEXg6jOKTFqqT8itfVL8PIVFycrvBMsohv/d1t+V+86e sdk-test@example.com';

        $mockClient = new MockClient([
            CreateSshKey::class => new ServerFixture('ssh-keys/create'),
            GetSshKeys::class => new ServerFixture('ssh-keys/list'),
            GetSshKey::class => new ServerFixture('ssh-keys/get'),
            DeleteSshKey::class => new ServerFixture('ssh-keys/delete'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);

        $createdId = null;
        try {
            $forge->server($serverId)->sshKeys()->create(
                new CreateSshKeyData(name: 'sdk-test-key', key: $publicKey),
            );

            // POST returned 202 with no body — recover the created key via list.
            // We can't match by name on replay (ForgeFixture redacts it), so we
            // pick the first key. During recording, the cleanup that runs
            // before this test guarantees the server has no other test keys.
            $page = $forge->server($serverId)->sshKeys()->all();

            expect($page->data)->not->toBeEmpty()
                ->and($page->data[0])->toBeInstanceOf(SshKey::class);

            $createdId = $page->data[0]->id;

            $fetched = $forge->server($serverId)->sshKey($createdId)->get();

            expect($fetched)->toBeInstanceOf(SshKey::class)
                ->and($fetched->id)->toBeString()->not->toBeEmpty();

            $forge->server($serverId)->sshKey($createdId)->delete();
            $createdId = null;
        } finally {
            if ($createdId !== null) {
                try {
                    $forge->server($serverId)->sshKey($createdId)->delete();
                } catch (Throwable) {
                    // Best-effort cleanup; the original failure surfaces.
                }
            }
        }
    });
