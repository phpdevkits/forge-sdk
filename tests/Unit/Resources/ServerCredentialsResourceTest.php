<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Data\Page;
use PhpDevKits\ForgeSdk\Data\ServerCredential;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\ServerCredentials\GetServerCredentials;
use Saloon\Http\Faking\MockClient;
use Tests\Utils\ServerCredentialFixture;

test('all() returns a Page<ServerCredential>',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $mockClient = new MockClient([
            GetServerCredentials::class => new ServerCredentialFixture('server-credentials/list'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);

        $page = $forge->serverCredentials()->all();

        expect($page)->toBeInstanceOf(Page::class)
            ->and($page->data)->not->toBeEmpty()
            ->and($page->data[0])->toBeInstanceOf(ServerCredential::class);
    });

test('iterate() yields each server credential',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $mockClient = new MockClient([
            GetServerCredentials::class => new ServerCredentialFixture('server-credentials/list'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);

        $credentials = iterator_to_array($forge->serverCredentials()->iterate());

        expect($credentials)->not->toBeEmpty()
            ->and($credentials[0])->toBeInstanceOf(ServerCredential::class);
    });
