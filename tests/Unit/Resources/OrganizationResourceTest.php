<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Data\Organization;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Organizations\GetOrganization;
use RuntimeException;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Tests\Utils\ForgeFixture;

test('get() returns a hydrated Organization',
    /**
     * @throws Throwable
     */
    function (): void {
        $slug = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org-1';

        $mockClient = new MockClient([
            GetOrganization::class => new ForgeFixture('organizations/get'),
        ]);
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $forge = new Forge($token)->withMockClient($mockClient);

        $organization = $forge->organization($slug)->get();

        expect($organization)->toBeInstanceOf(Organization::class)
            ->and($organization->id)->toBeString()->not->toBeEmpty()
            ->and($organization->slug)->toBeString()->not->toBeEmpty()
            ->and($organization->name)->toBeString();
    });

test('get() throws when the response has no data object',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetOrganization::class => MockResponse::make(['meta' => []]),
        ]);
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';
        $forge = new Forge($token)->withMockClient($mockClient);

        $forge->organization('acme')->get();
    })->throws(RuntimeException::class, 'Forge /orgs/{slug} response did not include a `data` object.');
