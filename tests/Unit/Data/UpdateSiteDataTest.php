<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use PhpDevKits\ForgeSdk\Data\UpdateSiteData;
use PhpDevKits\ForgeSdk\Enums\PhpVersion;
use PhpDevKits\ForgeSdk\Enums\SiteType;
use Tests\Factories\UpdateSiteDataFactory;

beforeEach(function (): void {
    $this->factory = new UpdateSiteDataFactory;
});

test('factory produces a usable UpdateSiteData',
    function (): void {
        expect($this->factory->make())->toBeInstanceOf(UpdateSiteData::class);
    });

test('toArray() is empty when nothing is set',
    function (): void {
        expect((new UpdateSiteData)->toArray())->toBe([]);
    });

test('toArray() emits root_path and directory when set',
    function (): void {
        expect(new UpdateSiteData(rootPath: '/srv', directory: '/public')->toArray())->toBe([
            'root_path' => '/srv',
            'directory' => '/public',
        ]);
    });

test('toArray() serializes enum fields',
    function (): void {
        $data = new UpdateSiteData(type: SiteType::Symfony, phpVersion: PhpVersion::Php83);

        expect($data->toArray())->toBe([
            'type' => 'symfony',
            'php_version' => 'php83',
        ]);
    });

test('toArray() emits push_to_deploy and repository_branch when set',
    function (): void {
        expect(new UpdateSiteData(pushToDeploy: true, repositoryBranch: 'release')->toArray())->toBe([
            'push_to_deploy' => true,
            'repository_branch' => 'release',
        ]);
    });
