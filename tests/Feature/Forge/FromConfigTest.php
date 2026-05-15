<?php

declare(strict_types=1);

namespace Tests\Feature\Forge;

use InvalidArgumentException;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Me\GetMeRequest;
use Saloon\Http\Faking\MockClient;
use Tests\Utils\ForgeFixture;

afterEach(function (): void {
    unset($_ENV['FORGE_CONFIG_PATH']);

    foreach ([sys_get_temp_dir().'/forge-test-config.json', getcwd().'/forge.json'] as $path) {
        if (is_file($path)) {
            unlink($path);
        }
    }
});

test('Forge::fromConfig() reads the token from a JSON file at the given path',
    /**
     * @throws Throwable
     */
    function (): void {
        $configPath = sys_get_temp_dir().'/forge-test-config.json';
        file_put_contents($configPath, json_encode(['token' => 'config-token-xyz']));

        $forge = Forge::fromConfig($configPath);

        $mockClient = new MockClient([
            GetMeRequest::class => new ForgeFixture('users/me'),
        ]);
        $forge->withMockClient($mockClient);
        $forge->me();

        $headers = $mockClient->getLastPendingRequest()?->headers();

        expect($headers?->get('Authorization'))->toBe('Bearer config-token-xyz');
    });

test('Forge::fromConfig() captures organization from JSON',
    /**
     * @throws Throwable
     */
    function (): void {
        $configPath = sys_get_temp_dir().'/forge-test-config.json';
        file_put_contents($configPath, json_encode(['token' => 'abc', 'organization' => 'acme']));

        $forge = Forge::fromConfig($configPath);

        expect($forge->organization)->toBe('acme');
    });

test('Forge::fromConfig() leaves organization null when absent from JSON',
    /**
     * @throws Throwable
     */
    function (): void {
        $configPath = sys_get_temp_dir().'/forge-test-config.json';
        file_put_contents($configPath, json_encode(['token' => 'abc']));

        $forge = Forge::fromConfig($configPath);

        expect($forge->organization)->toBeNull();
    });

test('Forge::fromConfig() defaults to ./forge.json when no path is given',
    /**
     * @throws Throwable
     */
    function (): void {
        file_put_contents(getcwd().'/forge.json', json_encode(['token' => 'cwd-token']));

        $forge = Forge::fromConfig();

        $mockClient = new MockClient([
            GetMeRequest::class => new ForgeFixture('users/me'),
        ]);
        $forge->withMockClient($mockClient);
        $forge->me();

        $headers = $mockClient->getLastPendingRequest()?->headers();

        expect($headers?->get('Authorization'))->toBe('Bearer cwd-token');
    });

test('Forge::fromConfig() honors FORGE_CONFIG_PATH when no path is given',
    /**
     * @throws Throwable
     */
    function (): void {
        $configPath = sys_get_temp_dir().'/forge-test-config.json';
        file_put_contents($configPath, json_encode(['token' => 'env-path-token']));
        $_ENV['FORGE_CONFIG_PATH'] = $configPath;

        $forge = Forge::fromConfig();

        $mockClient = new MockClient([
            GetMeRequest::class => new ForgeFixture('users/me'),
        ]);
        $forge->withMockClient($mockClient);
        $forge->me();

        $headers = $mockClient->getLastPendingRequest()?->headers();

        expect($headers?->get('Authorization'))->toBe('Bearer env-path-token');
    });

test('Forge::fromConfig() throws when the file is missing',
    /**
     * @throws InvalidArgumentException
     */
    function (): void {
        $missingPath = sys_get_temp_dir().'/forge-test-config.json';

        Forge::fromConfig($missingPath);
    })->throws(InvalidArgumentException::class, 'Forge config file not found');

test('Forge::fromConfig() throws when the file is not valid JSON',
    /**
     * @throws InvalidArgumentException
     */
    function (): void {
        $configPath = sys_get_temp_dir().'/forge-test-config.json';
        file_put_contents($configPath, '{not valid json');

        Forge::fromConfig($configPath);
    })->throws(InvalidArgumentException::class, 'is not valid JSON');

test('Forge::fromConfig() throws when the token key is missing',
    /**
     * @throws InvalidArgumentException
     */
    function (): void {
        $configPath = sys_get_temp_dir().'/forge-test-config.json';
        file_put_contents($configPath, json_encode(['organization' => 'acme']));

        Forge::fromConfig($configPath);
    })->throws(InvalidArgumentException::class, 'missing the required `token` key');

test('Forge::fromConfig() throws when the JSON root is not an object',
    /**
     * @throws InvalidArgumentException
     */
    function (): void {
        $configPath = sys_get_temp_dir().'/forge-test-config.json';
        file_put_contents($configPath, json_encode('a bare string, not an object'));

        Forge::fromConfig($configPath);
    })->throws(InvalidArgumentException::class, 'must contain a JSON object');
