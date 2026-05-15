<?php

declare(strict_types=1);

namespace Tests\Unit;

use InvalidArgumentException;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Resources\OrganizationResource;
use PhpDevKits\ForgeSdk\Resources\OrganizationsResource;
use PhpDevKits\ForgeSdk\Resources\ProviderResource;
use PhpDevKits\ForgeSdk\Resources\ProvidersResource;

afterEach(function (): void {
    unset(
        $_ENV['FORGE_TOKEN'],
        $_ENV['FORGE_ORGANIZATION'],
        $_ENV['FORGE_CONFIG_PATH'],
    );

    foreach ([sys_get_temp_dir().'/forge-test-config.json', getcwd().'/forge.json'] as $path) {
        if (is_file($path)) {
            unlink($path);
        }
    }
});

test('constructor captures the default organization slug',
    function (): void {
        $forge = new Forge('test-token', 'acme');

        expect($forge->defaultOrganization)->toBe('acme');
    });

test('constructor leaves defaultOrganization null when no slug is given',
    function (): void {
        $forge = new Forge('test-token');

        expect($forge->defaultOrganization)->toBeNull();
    });

test('Forge::fromEnvironment() reads the token from FORGE_TOKEN',
    function (): void {
        $_ENV['FORGE_TOKEN'] = 'env-token-abc';

        $forge = Forge::fromEnvironment();

        expect($forge)->toBeInstanceOf(Forge::class);
    });

test('Forge::fromEnvironment() captures FORGE_ORGANIZATION when set',
    function (): void {
        $_ENV['FORGE_TOKEN'] = 'env-token-abc';
        $_ENV['FORGE_ORGANIZATION'] = 'acme';

        $forge = Forge::fromEnvironment();

        expect($forge->defaultOrganization)->toBe('acme');
    });

test('Forge::fromEnvironment() leaves defaultOrganization null when FORGE_ORGANIZATION is unset',
    function (): void {
        $_ENV['FORGE_TOKEN'] = 'env-token-abc';

        $forge = Forge::fromEnvironment();

        expect($forge->defaultOrganization)->toBeNull();
    });

test('Forge::fromEnvironment() throws when FORGE_TOKEN is missing',
    function (): void {
        unset($_ENV['FORGE_TOKEN']);

        Forge::fromEnvironment();
    })->throws(InvalidArgumentException::class, 'FORGE_TOKEN environment variable is required.');

test('Forge::fromEnvironment() throws when FORGE_TOKEN is an empty string',
    function (): void {
        $_ENV['FORGE_TOKEN'] = '';

        Forge::fromEnvironment();
    })->throws(InvalidArgumentException::class, 'FORGE_TOKEN environment variable is required.');

test('Forge::fromConfig() reads the token from a JSON file at the given path',
    function (): void {
        $configPath = sys_get_temp_dir().'/forge-test-config.json';
        file_put_contents($configPath, json_encode(['token' => 'config-token-xyz']));

        $forge = Forge::fromConfig($configPath);

        expect($forge)->toBeInstanceOf(Forge::class);
    });

test('Forge::fromConfig() captures organization from JSON',
    function (): void {
        $configPath = sys_get_temp_dir().'/forge-test-config.json';
        file_put_contents($configPath, json_encode(['token' => 'abc', 'organization' => 'acme']));

        $forge = Forge::fromConfig($configPath);

        expect($forge->defaultOrganization)->toBe('acme');
    });

test('Forge::fromConfig() leaves defaultOrganization null when absent from JSON',
    function (): void {
        $configPath = sys_get_temp_dir().'/forge-test-config.json';
        file_put_contents($configPath, json_encode(['token' => 'abc']));

        $forge = Forge::fromConfig($configPath);

        expect($forge->defaultOrganization)->toBeNull();
    });

test('Forge::fromConfig() defaults to ./forge.json when no path is given',
    function (): void {
        file_put_contents(getcwd().'/forge.json', json_encode(['token' => 'cwd-token']));

        $forge = Forge::fromConfig();

        expect($forge)->toBeInstanceOf(Forge::class);
    });

test('Forge::fromConfig() honors FORGE_CONFIG_PATH when no path is given',
    function (): void {
        $configPath = sys_get_temp_dir().'/forge-test-config.json';
        file_put_contents($configPath, json_encode(['token' => 'env-path-token']));
        $_ENV['FORGE_CONFIG_PATH'] = $configPath;

        $forge = Forge::fromConfig();

        expect($forge)->toBeInstanceOf(Forge::class);
    });

test('Forge::fromConfig() throws when the file is missing',
    function (): void {
        $missingPath = sys_get_temp_dir().'/forge-test-config.json';

        Forge::fromConfig($missingPath);
    })->throws(InvalidArgumentException::class, 'Forge config file not found');

test('Forge::fromConfig() throws when the file is not valid JSON',
    function (): void {
        $configPath = sys_get_temp_dir().'/forge-test-config.json';
        file_put_contents($configPath, '{not valid json');

        Forge::fromConfig($configPath);
    })->throws(InvalidArgumentException::class, 'is not valid JSON');

test('Forge::fromConfig() throws when the token key is missing',
    function (): void {
        $configPath = sys_get_temp_dir().'/forge-test-config.json';
        file_put_contents($configPath, json_encode(['organization' => 'acme']));

        Forge::fromConfig($configPath);
    })->throws(InvalidArgumentException::class, 'missing the required `token` key');

test('Forge::fromConfig() throws when the JSON root is not an object',
    function (): void {
        $configPath = sys_get_temp_dir().'/forge-test-config.json';
        file_put_contents($configPath, json_encode('a bare string, not an object'));

        Forge::fromConfig($configPath);
    })->throws(InvalidArgumentException::class, 'must contain a JSON object');

test('organizations() returns an OrganizationsResource',
    function (): void {
        $forge = new Forge('test-token');

        expect($forge->organizations())->toBeInstanceOf(OrganizationsResource::class);
    });

test('organization($slug) returns an OrganizationResource',
    function (): void {
        $forge = new Forge('test-token');

        expect($forge->organization('acme'))->toBeInstanceOf(OrganizationResource::class);
    });

test('providers() returns a ProvidersResource',
    function (): void {
        $forge = new Forge('test-token');

        expect($forge->providers())->toBeInstanceOf(ProvidersResource::class);
    });

test('provider($slug) returns a ProviderResource',
    function (): void {
        $forge = new Forge('test-token');

        expect($forge->provider('digitalocean'))->toBeInstanceOf(ProviderResource::class);
    });
