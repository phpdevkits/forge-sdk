<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use Saloon\MockConfig;

/*
 * Load .env / .env.testing (in that order, testing wins) so FORGE_TEST_TOKEN
 * and friends are available when Saloon needs to record a missing fixture.
 */
foreach (['.env', '.env.testing'] as $envFile) {
    if (file_exists(dirname(__DIR__).'/'.$envFile)) {
        Dotenv::createImmutable(dirname(__DIR__), $envFile)->safeLoad();
    }
}

/*
 * Tell Saloon where fixtures live. Saloon's default behavior — record any
 * missing fixture from the live API — is left in place, matching the
 * ortto-sdk pattern. Run the suite once with real FORGE_TEST_* credentials
 * in .env to capture new fixtures; commit them so subsequent runs (and CI)
 * replay from disk.
 */
MockConfig::setFixturePath('tests/Fixtures/Saloon');
