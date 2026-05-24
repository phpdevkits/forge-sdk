<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Data\Deployment;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Servers\GetDeployment;
use Saloon\Http\Faking\MockClient;
use Tests\Utils\DeploymentFixture;

test('get() returns a hydrated Deployment',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $serverId = ($_ENV['FORGE_TEST_SERVER_ID'] ?? '') ?: '1';
        $siteId = ($_ENV['FORGE_TEST_SITE_ID'] ?? '') ?: '1';
        $deploymentId = ($_ENV['FORGE_TEST_DEPLOYMENT_ID'] ?? '') ?: '1';
        $mockClient = new MockClient([
            GetDeployment::class => new DeploymentFixture('deployments/get'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);

        $deployment = $forge->server($serverId)->site($siteId)->deployment($deploymentId)->get();

        expect($deployment)->toBeInstanceOf(Deployment::class)
            ->and($deployment->id)->toBeString()->not->toBeEmpty()
            ->and($deployment->status)->toBeString()->not->toBeEmpty();
    });
