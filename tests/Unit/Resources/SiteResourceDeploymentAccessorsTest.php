<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Data\Deployment;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Servers\TriggerDeployment;
use PhpDevKits\ForgeSdk\Resources\DeploymentResource;
use PhpDevKits\ForgeSdk\Resources\DeploymentScriptResource;
use PhpDevKits\ForgeSdk\Resources\DeploymentsResource;
use Saloon\Http\Faking\MockClient;
use Tests\Utils\DeploymentFixture;

test('site($id)->deploy() triggers a deployment and returns the Deployment',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $serverId = ($_ENV['FORGE_TEST_SERVER_ID'] ?? '') ?: '1';
        $siteId = ($_ENV['FORGE_TEST_SITE_ID'] ?? '') ?: '1';
        $mockClient = new MockClient([
            TriggerDeployment::class => new DeploymentFixture('deployments/trigger'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);

        $deployment = $forge->server($serverId)->site($siteId)->deploy();

        expect($deployment)->toBeInstanceOf(Deployment::class)
            ->and($deployment->id)->toBeString()->not->toBeEmpty();
    });

test('site($id)->deployments() returns a DeploymentsResource',
    function (): void {
        expect(new Forge('test-token', 'test-org')->server(42)->site(99)->deployments())
            ->toBeInstanceOf(DeploymentsResource::class);
    });

test('site($id)->deployment($deploymentId) returns a DeploymentResource',
    function (): void {
        expect(new Forge('test-token', 'test-org')->server(42)->site(99)->deployment(7))
            ->toBeInstanceOf(DeploymentResource::class);
    });

test('site($id)->deploymentScript() returns a DeploymentScriptResource',
    function (): void {
        expect(new Forge('test-token', 'test-org')->server(42)->site(99)->deploymentScript())
            ->toBeInstanceOf(DeploymentScriptResource::class);
    });
