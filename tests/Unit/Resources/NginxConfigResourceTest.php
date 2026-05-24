<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Data\NginxConfig;
use PhpDevKits\ForgeSdk\Data\UpdateNginxConfigData;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Servers\GetNginxConfig;
use PhpDevKits\ForgeSdk\Requests\Servers\UpdateNginxConfig;
use Saloon\Http\Faking\MockClient;
use Tests\Utils\NginxConfigFixture;

/*
 * Site Nginx-config read + write, against the configured site (recording) or
 * replayed from committed fixtures otherwise.
 *
 * Recording reads the live config and writes back the *same* content, so the
 * site is left unchanged. The fixture overwrites the `content` blob.
 *
 * Required env vars (recording only): FORGE_TEST_TOKEN, FORGE_TEST_ORG_SLUG,
 * FORGE_TEST_SERVER_ID, FORGE_TEST_SITE_ID.
 */
test('get() reads the Nginx config and update() writes it back',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $serverId = ($_ENV['FORGE_TEST_SERVER_ID'] ?? '') ?: '1';
        $siteId = ($_ENV['FORGE_TEST_SITE_ID'] ?? '') ?: '1';

        $mockClient = new MockClient([
            GetNginxConfig::class => new NginxConfigFixture('nginx/get'),
            UpdateNginxConfig::class => new NginxConfigFixture('nginx/update'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);

        $nginx = $forge->server($serverId)->site($siteId)->nginxConfig();

        $config = $nginx->get();
        expect($config)->toBeInstanceOf(NginxConfig::class)
            ->and($config->id)->toBeString()->not->toBeEmpty()
            ->and($config->content)->toBeString();

        // Write the same content back so the live site is unchanged.
        $nginx->update(new UpdateNginxConfigData(content: $config->content ?? ''));
    });
