<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use Saloon\MockConfig;

/*
 * Load .env / .env.testing (in that order, testing wins) so FORGE_TEST_TOKEN
 * and FORGE_RECORD_FIXTURES are available when re-recording Saloon fixtures
 * locally.
 */
foreach (['.env', '.env.testing'] as $envFile) {
    if (file_exists(dirname(__DIR__).'/'.$envFile)) {
        Dotenv::createImmutable(dirname(__DIR__), $envFile)->safeLoad();
    }
}

/*
 * Tell Saloon where fixtures live and, by default, fail loudly when one is
 * missing. To re-record (or capture a fixture for the first time):
 *
 *   FORGE_RECORD_FIXTURES=1 vendor/bin/pest
 *
 * In recording mode, missing fixtures are recorded by sending the real
 * request through to the Forge API. FORGE_TEST_TOKEN must be set.
 */
MockConfig::setFixturePath('tests/Fixtures/Saloon');

if (($_ENV['FORGE_RECORD_FIXTURES'] ?? null) !== '1') {
    MockConfig::throwOnMissingFixtures();
}
