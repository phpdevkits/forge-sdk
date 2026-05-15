<?php

declare(strict_types=1);

namespace Tests\Feature;

use InvalidArgumentException;
use PhpDevKits\ForgeSdk\Data\User;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Me\GetMeRequest;
use RuntimeException;
use Saloon\Http\Faking\MockClient;
use Tests\Utils\ForgeFixture;

beforeEach(function (): void {
    $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';

    $this->mockClient = new MockClient([
        GetMeRequest::class => new ForgeFixture('users/me'),
    ]);

    $this->forge = new Forge($token)->withMockClient($this->mockClient);
});

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

test('returns the authenticated user from /me',
    /**
     * @throws Throwable
     */
    function (): void {
        $user = $this->forge->me();

        expect($user)->toBeInstanceOf(User::class)
            ->and($user->id)->toBeString()->not->toBeEmpty()
            ->and($user->name)->toBeString()->not->toBeEmpty()
            ->and($user->email)->toBeString()->toContain('@');
    });

test('sends the /me request to the forge.laravel.com/api base URL',
    /**
     * @throws Throwable
     */
    function (): void {
        $this->forge->me();

        $url = $this->mockClient->getLastPendingRequest()?->getUrl() ?? '';

        expect($url)->toBe('https://forge.laravel.com/api/me');
    });

test('sends the bearer token in the Authorization header',
    /**
     * @throws Throwable
     */
    function (): void {
        $forge = new Forge('secret-pat-123')->withMockClient($this->mockClient);

        $forge->me();

        $headers = $this->mockClient->getLastPendingRequest()?->headers();

        expect($headers?->get('Authorization'))->toBe('Bearer secret-pat-123');
    });

test('negotiates JSON:API content type',
    /**
     * @throws Throwable
     */
    function (): void {
        $this->forge->me();

        $headers = $this->mockClient->getLastPendingRequest()?->headers();

        expect($headers?->get('Accept'))->toBe('application/vnd.api+json')
            ->and($headers?->get('Content-Type'))->toBe('application/vnd.api+json');
    });

test('throws when the /me response has no data object',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetMeRequest::class => new ForgeFixture('users/me-missing-data'),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        expect(fn (): User => $forge->me())
            ->toThrow(RuntimeException::class, 'Forge /me response did not include a `data` object.');
    });

test('Forge::fromEnvironment() reads the token from FORGE_TOKEN',
    /**
     * @throws Throwable
     */
    function (): void {
        $_ENV['FORGE_TOKEN'] = 'env-token-abc';

        $forge = Forge::fromEnvironment();

        $mockClient = new MockClient([
            GetMeRequest::class => new ForgeFixture('users/me'),
        ]);
        $forge->withMockClient($mockClient);

        $forge->me();

        $headers = $mockClient->getLastPendingRequest()?->headers();

        expect($headers?->get('Authorization'))->toBe('Bearer env-token-abc');
    });

test('Forge::fromEnvironment() captures FORGE_ORGANIZATION when set',
    /**
     * @throws Throwable
     */
    function (): void {
        $_ENV['FORGE_TOKEN'] = 'env-token-abc';
        $_ENV['FORGE_ORGANIZATION'] = 'acme';

        $forge = Forge::fromEnvironment();

        expect($forge->organization)->toBe('acme');
    });

test('Forge::fromEnvironment() leaves organization null when FORGE_ORGANIZATION is unset',
    /**
     * @throws Throwable
     */
    function (): void {
        $_ENV['FORGE_TOKEN'] = 'env-token-abc';

        $forge = Forge::fromEnvironment();

        expect($forge->organization)->toBeNull();
    });

test('Forge::fromEnvironment() throws when FORGE_TOKEN is missing',
    /**
     * @throws InvalidArgumentException
     */
    function (): void {
        unset($_ENV['FORGE_TOKEN']);

        expect(fn (): Forge => Forge::fromEnvironment())
            ->toThrow(InvalidArgumentException::class, 'FORGE_TOKEN environment variable is required.');
    });

test('Forge::fromEnvironment() throws when FORGE_TOKEN is an empty string',
    /**
     * @throws InvalidArgumentException
     */
    function (): void {
        $_ENV['FORGE_TOKEN'] = '';

        expect(fn (): Forge => Forge::fromEnvironment())
            ->toThrow(InvalidArgumentException::class, 'FORGE_TOKEN environment variable is required.');
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

        expect(fn (): Forge => Forge::fromConfig($missingPath))
            ->toThrow(InvalidArgumentException::class, 'Forge config file not found');
    });

test('Forge::fromConfig() throws when the file is not valid JSON',
    /**
     * @throws InvalidArgumentException
     */
    function (): void {
        $configPath = sys_get_temp_dir().'/forge-test-config.json';
        file_put_contents($configPath, '{not valid json');

        expect(fn (): Forge => Forge::fromConfig($configPath))
            ->toThrow(InvalidArgumentException::class, 'is not valid JSON');
    });

test('Forge::fromConfig() throws when the token key is missing',
    /**
     * @throws InvalidArgumentException
     */
    function (): void {
        $configPath = sys_get_temp_dir().'/forge-test-config.json';
        file_put_contents($configPath, json_encode(['organization' => 'acme']));

        expect(fn (): Forge => Forge::fromConfig($configPath))
            ->toThrow(InvalidArgumentException::class, 'missing the required `token` key');
    });

test('Forge::fromConfig() throws when the JSON root is not an object',
    /**
     * @throws InvalidArgumentException
     */
    function (): void {
        $configPath = sys_get_temp_dir().'/forge-test-config.json';
        file_put_contents($configPath, json_encode('a bare string, not an object'));

        expect(fn (): Forge => Forge::fromConfig($configPath))
            ->toThrow(InvalidArgumentException::class, 'must contain a JSON object');
    });
