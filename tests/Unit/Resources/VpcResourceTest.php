<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Data\Vpc;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\ServerCredentials\GetVpc;
use Saloon\Http\Faking\MockClient;
use Tests\Utils\VpcFixture;

test('get() returns a Vpc',
    /**
     * @throws Throwable
     */
    function (): void {
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $org = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org';
        $credentialId = ($_ENV['FORGE_TEST_HETZNER_CREDENTIAL_ID'] ?? '') ?: '1';
        $region = ($_ENV['FORGE_TEST_HETZNER_REGION'] ?? '') ?: '1';
        $vpcId = ($_ENV['FORGE_TEST_VPC_ID'] ?? '') ?: '1';
        $mockClient = new MockClient([
            GetVpc::class => new VpcFixture('server-credentials/vpcs/get'),
        ]);
        $forge = new Forge($token, $org)->withMockClient($mockClient);

        $vpc = $forge->serverCredential($credentialId)->region($region)->vpc($vpcId)->get();

        expect($vpc)->toBeInstanceOf(Vpc::class)
            ->and($vpc->id)->toBeString()->not->toBeEmpty()
            ->and($vpc->subnets)->toBeArray();
    });
