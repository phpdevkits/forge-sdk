<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use PhpDevKits\ForgeSdk\Data\RestoreBackupData;
use Tests\Factories\RestoreBackupDataFactory;

beforeEach(function (): void {
    $this->factory = new RestoreBackupDataFactory;
});

test('factory produces a usable RestoreBackupData',
    function (): void {
        expect($this->factory->make())->toBeInstanceOf(RestoreBackupData::class);
    });

test('toArray() emits the required database_id field',
    function (): void {
        expect((new RestoreBackupData(databaseId: 12345))->toArray())->toBe([
            'database_id' => 12345,
        ]);
    });
