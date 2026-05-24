<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Data\UpdateEnvironmentData;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Servers\GetEnvironment;
use PhpDevKits\ForgeSdk\Requests\Servers\UpdateEnvironment;
use Saloon\Http\Faking\MockClient;
use Tests\Utils\EnvironmentFixture;

/*
 * Site environment-file read + write, against the configured site (recording)
 * or replayed from committed fixtures otherwise.
 *
 * Recording reads the live `.env` and writes back the *same* content, so the
 * site is left unchanged. The fixture overwrites the secret `content` blob.
 *
 * Required env vars (recording only): FORGE_TEST_TOKEN, FORGE_TEST_ORG_SLUG,
 * FORGE_TEST_SERVER_ID, FORGE_TEST_SITE_ID.
 */
test('get() reads the .env and update() writes it back',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $serverId = ($_ENV['FORGE_TEST_SERVER_ID'] ?? '') ?: '1';
        $siteId = ($_ENV['FORGE_TEST_SITE_ID'] ?? '') ?: '1';

        $mockClient = new MockClient([
            GetEnvironment::class => new EnvironmentFixture('environment/get'),
            UpdateEnvironment::class => new EnvironmentFixture('environment/update'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);

        $environment = $forge->server($serverId)->site($siteId)->environment();

        $content = $environment->get();
        expect($content)->toBeString()->not->toBeEmpty();

        // Write the same content back so the live site is unchanged.
        $environment->update(new UpdateEnvironmentData(content: $content));
    });
