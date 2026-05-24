<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use PhpDevKits\ForgeSdk\Data\CreateSiteData;
use PhpDevKits\ForgeSdk\Enums\PhpVersion;
use PhpDevKits\ForgeSdk\Enums\SiteType;
use PhpDevKits\ForgeSdk\Enums\WwwRedirectType;
use Tests\Factories\CreateSiteDataFactory;

beforeEach(function (): void {
    $this->factory = new CreateSiteDataFactory;
});

test('factory produces a usable CreateSiteData',
    function (): void {
        expect($this->factory->make())->toBeInstanceOf(CreateSiteData::class);
    });

test('toArray() emits only the required type when nothing else is set',
    function (): void {
        expect(new CreateSiteData(type: SiteType::Laravel)->toArray())->toBe(['type' => 'laravel']);
    });

test('toArray() serializes enum fields to their values',
    function (): void {
        $data = new CreateSiteData(
            type: SiteType::Php,
            wwwRedirectType: WwwRedirectType::FromWww,
            phpVersion: PhpVersion::Php84,
        );

        $payload = $data->toArray();

        expect($payload['type'])->toBe('php')
            ->and($payload['www_redirect_type'])->toBe('from-www')
            ->and($payload['php_version'])->toBe('php84');
    });

test('toArray() emits scalar create fields when set',
    function (): void {
        $data = new CreateSiteData(
            type: SiteType::Laravel,
            name: 'example.com',
            rootDirectory: '/home/forge/example.com',
            webDirectory: '/public',
            isIsolated: true,
            isolatedUser: 'example',
            zeroDowntimeDeployments: true,
            sourceControlProvider: 'github',
            repository: 'acme/site',
            branch: 'main',
            databaseId: 10,
            databaseUserId: 20,
        );

        expect($data->toArray())->toBe([
            'type' => 'laravel',
            'name' => 'example.com',
            'root_directory' => '/home/forge/example.com',
            'web_directory' => '/public',
            'is_isolated' => true,
            'isolated_user' => 'example',
            'zero_downtime_deployments' => true,
            'source_control_provider' => 'github',
            'repository' => 'acme/site',
            'branch' => 'main',
            'database_id' => 10,
            'database_user_id' => 20,
        ]);
    });

test('toArray() emits domain_mode when set',
    function (): void {
        expect(new CreateSiteData(type: SiteType::Php, domainMode: 'on-forge')->toArray())->toBe([
            'type' => 'php',
            'domain_mode' => 'on-forge',
        ]);
    });
