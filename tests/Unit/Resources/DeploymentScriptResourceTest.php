<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Data\DeploymentScript;
use PhpDevKits\ForgeSdk\Data\UpdateDeploymentScriptData;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Servers\GetDeploymentScript;
use PhpDevKits\ForgeSdk\Requests\Servers\UpdateDeploymentScript;
use Saloon\Http\Faking\MockClient;
use Tests\Utils\DeploymentFixture;

test('get() returns the deployment script',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $serverId = ($_ENV['FORGE_TEST_SERVER_ID'] ?? '') ?: '1';
        $siteId = ($_ENV['FORGE_TEST_SITE_ID'] ?? '') ?: '1';
        $mockClient = new MockClient([
            GetDeploymentScript::class => new DeploymentFixture('deployments/script'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);

        $script = $forge->server($serverId)->site($siteId)->deploymentScript()->get();

        expect($script)->toBeInstanceOf(DeploymentScript::class)
            ->and($script->id)->toBeString()->not->toBeEmpty();
    });

test('update() returns the updated deployment script',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $serverId = ($_ENV['FORGE_TEST_SERVER_ID'] ?? '') ?: '1';
        $siteId = ($_ENV['FORGE_TEST_SITE_ID'] ?? '') ?: '1';
        $mockClient = new MockClient([
            UpdateDeploymentScript::class => new DeploymentFixture('deployments/script-update'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);

        $script = $forge->server($serverId)->site($siteId)->deploymentScript()->update(
            new UpdateDeploymentScriptData(content: "cd /home/forge/example.com\ngit pull origin main"),
        );

        expect($script)->toBeInstanceOf(DeploymentScript::class)
            ->and($script->id)->toBeString()->not->toBeEmpty();
    });
