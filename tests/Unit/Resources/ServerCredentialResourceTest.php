<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Data\ServerCredential;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\ServerCredentials\GetServerCredential;
use Saloon\Http\Faking\MockClient;
use Tests\Utils\ServerCredentialFixture;

test('get() returns a ServerCredential',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $credentialId = ($_ENV['FORGE_TEST_HETZNER_CREDENTIAL_ID'] ?? '') ?: '1';
        $mockClient = new MockClient([
            GetServerCredential::class => new ServerCredentialFixture('server-credentials/get'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);

        $credential = $forge->serverCredential($credentialId)->get();

        expect($credential)->toBeInstanceOf(ServerCredential::class)
            ->and($credential->id)->toBeString()->not->toBeEmpty()
            ->and($credential->provider)->toBeString();
    });
