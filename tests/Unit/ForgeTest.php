<?php

declare(strict_types=1);

namespace Tests\Unit;

use InvalidArgumentException;
use PhpDevKits\ForgeSdk\Data\User;
use PhpDevKits\ForgeSdk\Exceptions\ApiException;
use PhpDevKits\ForgeSdk\Exceptions\BadRequestException;
use PhpDevKits\ForgeSdk\Exceptions\ConnectionException;
use PhpDevKits\ForgeSdk\Exceptions\ForbiddenException;
use PhpDevKits\ForgeSdk\Exceptions\NotFoundException;
use PhpDevKits\ForgeSdk\Exceptions\RateLimitException;
use PhpDevKits\ForgeSdk\Exceptions\ServerException;
use PhpDevKits\ForgeSdk\Exceptions\UnauthorizedException;
use PhpDevKits\ForgeSdk\Exceptions\ValidationException;
use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Requests\Me\GetMe;
use RuntimeException;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\PendingRequest;
use Tests\Utils\ForgeFixture;

beforeEach(function (): void {
    $token = ($_ENV['FORGE_TEST_TOKEN'] ?? '') ?: 'test-token';

    $this->mockClient = new MockClient([
        GetMe::class => new ForgeFixture('me/me'),
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

// ---------------------------------------------------------------------------
// me()
// ---------------------------------------------------------------------------

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
            GetMe::class => new ForgeFixture('me/me-missing-data'),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        $forge->me();
    })->throws(RuntimeException::class, 'Forge /me response did not include a `data` object.');

// ---------------------------------------------------------------------------
// fromEnvironment()
// ---------------------------------------------------------------------------

test('Forge::fromEnvironment() reads the token from FORGE_TOKEN',
    /**
     * @throws Throwable
     */
    function (): void {
        $_ENV['FORGE_TOKEN'] = 'env-token-abc';

        $forge = Forge::fromEnvironment();

        $mockClient = new MockClient([
            GetMe::class => new ForgeFixture('me/me'),
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

        Forge::fromEnvironment();
    })->throws(InvalidArgumentException::class, 'FORGE_TOKEN environment variable is required.');

test('Forge::fromEnvironment() throws when FORGE_TOKEN is an empty string',
    /**
     * @throws InvalidArgumentException
     */
    function (): void {
        $_ENV['FORGE_TOKEN'] = '';

        Forge::fromEnvironment();
    })->throws(InvalidArgumentException::class, 'FORGE_TOKEN environment variable is required.');

// ---------------------------------------------------------------------------
// fromConfig()
// ---------------------------------------------------------------------------

test('Forge::fromConfig() reads the token from a JSON file at the given path',
    /**
     * @throws Throwable
     */
    function (): void {
        $configPath = sys_get_temp_dir().'/forge-test-config.json';
        file_put_contents($configPath, json_encode(['token' => 'config-token-xyz']));

        $forge = Forge::fromConfig($configPath);

        $mockClient = new MockClient([
            GetMe::class => new ForgeFixture('me/me'),
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
            GetMe::class => new ForgeFixture('me/me'),
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
            GetMe::class => new ForgeFixture('me/me'),
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

// ---------------------------------------------------------------------------
// Status -> exception mapping
// ---------------------------------------------------------------------------

test('throws BadRequestException on a 400 response',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetMe::class => new ForgeFixture('me/me-400'),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        $forge->me();
    })->throws(BadRequestException::class);

test('throws UnauthorizedException on a 401 response',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetMe::class => new ForgeFixture('me/me-401'),
        ]);
        $forge = new Forge('bad-token')->withMockClient($mockClient);

        $forge->me();
    })->throws(UnauthorizedException::class);

test('throws ForbiddenException on a 403 response',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetMe::class => new ForgeFixture('me/me-403'),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        $forge->me();
    })->throws(ForbiddenException::class);

test('throws NotFoundException on a 404 response',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetMe::class => new ForgeFixture('me/me-404'),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        $forge->me();
    })->throws(NotFoundException::class);

test('throws ValidationException on a 422 response',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetMe::class => new ForgeFixture('me/me-422'),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        $forge->me();
    })->throws(ValidationException::class);

test('ValidationException exposes the parsed Laravel error bag',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetMe::class => new ForgeFixture('me/me-422'),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        expect(fn (): User => $forge->me())->toThrow(function (ValidationException $validationException): void {
            expect($validationException->errors())->toHaveKey('name')
                ->and($validationException->errors())->toHaveKey('email')
                ->and($validationException->errorsFor('name'))->toBe([
                    'The name field is required.',
                    'The name must be at least 3 characters.',
                ])
                ->and($validationException->firstError('name'))->toBe('The name field is required.')
                ->and($validationException->firstError('nonexistent'))->toBeNull();
        });
    });

test('ValidationException::errors() returns an empty array when no errors key is present',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetMe::class => new ForgeFixture('me/me-422-empty'),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        expect(fn (): User => $forge->me())->toThrow(function (ValidationException $validationException): void {
            expect($validationException->errors())->toBe([])
                ->and($validationException->errorsFor('anything'))->toBe([])
                ->and($validationException->firstError('anything'))->toBeNull();
        });
    });

test('ValidationException::errors() skips entries with non-string keys or non-array messages',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetMe::class => new ForgeFixture('me/me-422-bad-shape'),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        expect(fn (): User => $forge->me())->toThrow(function (ValidationException $validationException): void {
            expect($validationException->errors())->toBe([]);
        });
    });

test('throws RateLimitException on a 429 response',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetMe::class => new ForgeFixture('me/me-429'),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        $forge->me();
    })->throws(RateLimitException::class);

test('RateLimitException parses the Retry-After header',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetMe::class => new ForgeFixture('me/me-429'),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        expect(fn (): User => $forge->me())->toThrow(function (RateLimitException $rateLimitException): void {
            expect($rateLimitException->retryAfter())->toBe(60);
        });
    });

test('RateLimitException::retryAfter() returns null when the header is missing',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetMe::class => new ForgeFixture('me/me-429-no-header'),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        expect(fn (): User => $forge->me())->toThrow(function (RateLimitException $rateLimitException): void {
            expect($rateLimitException->retryAfter())->toBeNull();
        });
    });

test('throws ServerException on a 500 response',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetMe::class => new ForgeFixture('me/me-500'),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        $forge->me();
    })->throws(ServerException::class);

test('throws ServerException on any 5xx response',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetMe::class => new ForgeFixture('me/me-503'),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        $forge->me();
    })->throws(ServerException::class);

test('falls back to ApiException for an unknown status code',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetMe::class => new ForgeFixture('me/me-418'),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        $forge->me();
    })->throws(ApiException::class);

test('ApiException::status() returns the response status code',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetMe::class => new ForgeFixture('me/me-404'),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        expect(fn (): User => $forge->me())->toThrow(function (NotFoundException $notFoundException): void {
            expect($notFoundException->status())->toBe(404);
        });
    });

test('wraps Saloon transport errors in ConnectionException',
    /**
     * @throws Throwable
     */
    function (): void {
        $mockClient = new MockClient([
            GetMe::class => MockResponse::make()->throw(
                fn (PendingRequest $pendingRequest): FatalRequestException => new FatalRequestException(
                    new RuntimeException('Connection refused'),
                    $pendingRequest,
                ),
            ),
        ]);
        $forge = new Forge('test-token')->withMockClient($mockClient);

        $forge->me();
    })->throws(ConnectionException::class, 'Forge API connection failed');
