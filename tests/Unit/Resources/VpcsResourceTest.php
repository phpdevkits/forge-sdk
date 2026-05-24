<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Data\CreateVpcData;
use PhpDevKits\ForgeSdk\Data\Page;
use PhpDevKits\ForgeSdk\Data\Vpc;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\ServerCredentials\CreateVpc;
use PhpDevKits\ForgeSdk\Requests\ServerCredentials\GetVpcs;
use Saloon\Http\Faking\MockClient;
use Tests\Utils\VpcFixture;

test('all() returns a Page<Vpc>',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $credentialId = ($_ENV['FORGE_TEST_HETZNER_CREDENTIAL_ID'] ?? '') ?: '1';
        $region = ($_ENV['FORGE_TEST_HETZNER_REGION'] ?? '') ?: '1';
        $mockClient = new MockClient([
            GetVpcs::class => new VpcFixture('server-credentials/vpcs/list'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);

        $page = $forge->serverCredential($credentialId)->region($region)->all();

        expect($page)->toBeInstanceOf(Page::class)
            ->and($page->data)->not->toBeEmpty()
            ->and($page->data[0])->toBeInstanceOf(Vpc::class);
    });

test('iterate() yields each VPC',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $credentialId = ($_ENV['FORGE_TEST_HETZNER_CREDENTIAL_ID'] ?? '') ?: '1';
        $region = ($_ENV['FORGE_TEST_HETZNER_REGION'] ?? '') ?: '1';
        $mockClient = new MockClient([
            GetVpcs::class => new VpcFixture('server-credentials/vpcs/list'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);

        $vpcs = iterator_to_array($forge->serverCredential($credentialId)->region($region)->iterate());

        expect($vpcs)->not->toBeEmpty()
            ->and($vpcs[0])->toBeInstanceOf(Vpc::class);
    });

test('create() provisions and returns a Vpc',
    /**
     * NOTE: the Forge API has no VPC delete endpoint, so a recording run leaves
     * a permanent `sdk-test-vpc` network on the account (remove it by hand in
     * the Forge/Hetzner UI). Replays from the committed fixture touch nothing.
     *
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $credentialId = ($_ENV['FORGE_TEST_HETZNER_CREDENTIAL_ID'] ?? '') ?: '1';
        $region = ($_ENV['FORGE_TEST_HETZNER_REGION'] ?? '') ?: '1';
        $mockClient = new MockClient([
            CreateVpc::class => new VpcFixture('server-credentials/vpcs/create'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);

        $vpc = $forge->serverCredential($credentialId)->region($region)->create(
            new CreateVpcData(name: 'sdk-test-vpc'),
        );

        expect($vpc)->toBeInstanceOf(Vpc::class)
            ->and($vpc->id)->toBeString()->not->toBeEmpty();
    });
