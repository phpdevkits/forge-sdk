<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use DateTimeImmutable;
use InvalidArgumentException;
use PhpDevKits\ForgeSdk\Data\Site;
use PhpDevKits\ForgeSdk\Data\SiteMaintenanceMode;
use PhpDevKits\ForgeSdk\Data\SiteRepository;
use Tests\Factories\SiteFactory;

beforeEach(function (): void {
    $this->factory = new SiteFactory;

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    $this->siteAttributes = static fn (array $overrides = []): array => array_merge([
        'name' => 'example.com',
        'status' => 'installed',
        'url' => 'https://example.com',
        'user' => 'forge',
        'https' => true,
        'web_directory' => '/public',
        'root_directory' => '/home/forge/example.com',
        'aliases' => ['www.example.com'],
        'php_version' => 'php84',
        'deployment_status' => 'finished',
        'quick_deploy' => true,
        'isolated' => false,
        'shared_paths' => ['storage' => '/home/forge/shared/storage'],
        'repository' => ['provider' => 'github', 'url' => 'acme/site', 'branch' => 'main', 'status' => 'installed'],
        'database' => 'forge',
        'maintenance_mode' => ['enabled' => false, 'status' => null],
        'zero_downtime_deployments' => true,
        'deployment_script' => 'php artisan migrate --force',
        'wildcards' => false,
        'app_type' => 'laravel',
        'uses_envoyer' => false,
        'deployment_url' => 'https://forge.laravel.com/deploy/http/secret',
        'healthcheck_url' => null,
        'created_at' => '2026-01-01T00:00:00Z',
        'updated_at' => '2026-05-01T00:00:00Z',
    ], $overrides);
});

test('factory produces a usable Site',
    function (): void {
        $site = $this->factory->make();

        expect($site)->toBeInstanceOf(Site::class)
            ->and($site->repository)->toBeInstanceOf(SiteRepository::class)
            ->and($site->maintenanceMode)->toBeInstanceOf(SiteMaintenanceMode::class);
    });

test('::from() hydrates a full JSON:API resource object',
    function (): void {
        $site = Site::from(['id' => '7', 'type' => 'sites', 'attributes' => ($this->siteAttributes)()]);

        expect($site->id)->toBe('7')
            ->and($site->name)->toBe('example.com')
            ->and($site->https)->toBeTrue()
            ->and($site->aliases)->toBe(['www.example.com'])
            ->and($site->sharedPaths)->toBe(['storage' => '/home/forge/shared/storage'])
            ->and($site->repository)->toBeInstanceOf(SiteRepository::class)
            ->and($site->repository->provider)->toBe('github')
            ->and($site->maintenanceMode->enabled)->toBeFalse()
            ->and($site->zeroDowntimeDeployments)->toBeTrue()
            ->and($site->createdAt->format('Y-m-d'))->toBe('2026-01-01');
    });

test('::from() hydrates a freshly-created site (aliases/deployment_status/maintenance enabled null)',
    function (): void {
        $site = Site::from(['id' => '1', 'attributes' => ($this->siteAttributes)([
            'root_directory' => null,
            'aliases' => null,
            'deployment_status' => null,
            'quick_deploy' => null,
            'database' => null,
            'deployment_script' => null,
            'wildcards' => null,
            'app_type' => null,
            'healthcheck_url' => null,
            'maintenance_mode' => ['enabled' => null, 'status' => null],
        ])]);

        expect($site->rootDirectory)->toBeNull()
            ->and($site->aliases)->toBe([])
            ->and($site->deploymentStatus)->toBeNull()
            ->and($site->quickDeploy)->toBeNull()
            ->and($site->database)->toBeNull()
            ->and($site->wildcards)->toBeNull()
            ->and($site->appType)->toBeNull()
            ->and($site->healthcheckUrl)->toBeNull()
            ->and($site->maintenanceMode->enabled)->toBeNull();
    });

test('::from() throws when aliases is a non-array, non-null value',
    function (): void {
        Site::from(['id' => '1', 'attributes' => ($this->siteAttributes)(['aliases' => 'nope'])]);
    })->throws(InvalidArgumentException::class, '`attributes.aliases` must be an array or null');

test('::from() throws when id is missing',
    function (): void {
        Site::from(['attributes' => []]);
    })->throws(InvalidArgumentException::class, 'missing the `id` field');

test('::from() throws when attributes is missing',
    function (): void {
        Site::from(['id' => '1']);
    })->throws(InvalidArgumentException::class, 'missing the `attributes` object');

test('::from() throws when repository object is missing',
    function (): void {
        $attrs = ($this->siteAttributes)();
        unset($attrs['repository']);
        Site::from(['id' => '1', 'attributes' => $attrs]);
    })->throws(InvalidArgumentException::class, '`attributes.repository` must be an object');

test('::from() throws when maintenance_mode object is missing',
    function (): void {
        $attrs = ($this->siteAttributes)();
        unset($attrs['maintenance_mode']);
        Site::from(['id' => '1', 'attributes' => $attrs]);
    })->throws(InvalidArgumentException::class, '`attributes.maintenance_mode` must be an object');

test('::from() throws when a required string field is missing',
    function (): void {
        $attrs = ($this->siteAttributes)();
        unset($attrs['name']);
        Site::from(['id' => '1', 'attributes' => $attrs]);
    })->throws(InvalidArgumentException::class, '`attributes.name` must be a string');

test('::from() throws when a required bool field is missing',
    function (): void {
        $attrs = ($this->siteAttributes)();
        unset($attrs['https']);
        Site::from(['id' => '1', 'attributes' => $attrs]);
    })->throws(InvalidArgumentException::class, '`attributes.https` must be a boolean');

test('::from() throws when a required date is malformed',
    function (): void {
        Site::from(['id' => '1', 'attributes' => ($this->siteAttributes)(['created_at' => 'not-a-date'])]);
    })->throws(InvalidArgumentException::class, '`attributes.created_at` is not a valid date-time');

test('::from() throws when a required date is not a string',
    function (): void {
        Site::from(['id' => '1', 'attributes' => ($this->siteAttributes)(['created_at' => 123])]);
    })->throws(InvalidArgumentException::class, '`attributes.created_at` must be a string');

test('::from() throws when an optional string field is the wrong type',
    function (): void {
        Site::from(['id' => '1', 'attributes' => ($this->siteAttributes)(['root_directory' => 42])]);
    })->throws(InvalidArgumentException::class, '`attributes.root_directory` must be a string or null');

test('::from() throws when an optional bool field is the wrong type',
    function (): void {
        Site::from(['id' => '1', 'attributes' => ($this->siteAttributes)(['quick_deploy' => 'yes'])]);
    })->throws(InvalidArgumentException::class, '`attributes.quick_deploy` must be a boolean or null');

test('::from() throws when shared_paths is not an object',
    function (): void {
        Site::from(['id' => '1', 'attributes' => ($this->siteAttributes)(['shared_paths' => 'nope'])]);
    })->throws(InvalidArgumentException::class, '`attributes.shared_paths` must be an object');

test('jsonSerialize() emits snake_case with nested objects and ATOM dates',
    function (): void {
        $site = $this->factory->make([
            'id' => '9',
            'createdAt' => new DateTimeImmutable('2026-01-01T00:00:00+00:00'),
            'updatedAt' => new DateTimeImmutable('2026-02-01T00:00:00+00:00'),
        ]);

        $json = $site->jsonSerialize();

        expect($json['id'])->toBe('9')
            ->and($json)->toHaveKey('web_directory')
            ->and($json)->toHaveKey('zero_downtime_deployments')
            ->and($json['repository'])->toBeInstanceOf(SiteRepository::class)
            ->and($json['created_at'])->toBe('2026-01-01T00:00:00+00:00');
    });
