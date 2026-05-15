<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Organizations;

use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Organizations\GetOrganization;
use Saloon\Http\Faking\MockClient;
use Tests\Utils\ForgeFixture;

test('sends a GET to /orgs/{slug}',
    /**
     * @throws Throwable
     */
    function (): void {
        $slug = ($_ENV['FORGE_TEST_ORG_SLUG'] ?? '') ?: 'test-org-1';
        $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';

        $mockClient = new MockClient([
            GetOrganization::class => new ForgeFixture('organizations/get'),
        ]);
        $forge = new Forge($token)->withMockClient($mockClient);

        $forge->organization($slug)->get();

        $url = $mockClient->getLastPendingRequest()?->getUrl() ?? '';

        expect($url)->toBe('https://forge.laravel.com/api/orgs/'.$slug);
    });
