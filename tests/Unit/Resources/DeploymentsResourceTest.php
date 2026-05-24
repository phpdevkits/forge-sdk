<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Data\Deployment;
use PhpDevKits\ForgeSdk\Data\Page;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Servers\GetDeployments;
use PhpDevKits\ForgeSdk\Requests\Servers\TriggerDeployment;
use Saloon\Http\Faking\MockClient;
use Tests\Utils\DeploymentFixture;

test('all() returns a Page<Deployment>',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $serverId = ($_ENV['FORGE_TEST_SERVER_ID'] ?? '') ?: '1';
        $siteId = ($_ENV['FORGE_TEST_SITE_ID'] ?? '') ?: '1';
        $mockClient = new MockClient([
            GetDeployments::class => new DeploymentFixture('deployments/list'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);

        $page = $forge->server($serverId)->site($siteId)->deployments()->all();

        expect($page)->toBeInstanceOf(Page::class)
            ->and($page->data)->not->toBeEmpty()
            ->and($page->data[0])->toBeInstanceOf(Deployment::class);
    });

test('iterate() yields each deployment',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $serverId = ($_ENV['FORGE_TEST_SERVER_ID'] ?? '') ?: '1';
        $siteId = ($_ENV['FORGE_TEST_SITE_ID'] ?? '') ?: '1';
        $mockClient = new MockClient([
            GetDeployments::class => new DeploymentFixture('deployments/list'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);

        $deployments = iterator_to_array($forge->server($serverId)->site($siteId)->deployments()->iterate());

        expect($deployments)->not->toBeEmpty()
            ->and($deployments[0])->toBeInstanceOf(Deployment::class);
    });

test('trigger() returns the new Deployment',
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

        $deployment = $forge->server($serverId)->site($siteId)->deployments()->trigger();

        expect($deployment)->toBeInstanceOf(Deployment::class)
            ->and($deployment->id)->toBeString()->not->toBeEmpty();
    });
