<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use PhpDevKits\ForgeSdk\Data\UpdateNginxConfigData;
use Tests\Factories\UpdateNginxConfigDataFactory;

beforeEach(function (): void {
    $this->factory = new UpdateNginxConfigDataFactory;
});

test('factory produces a usable UpdateNginxConfigData',
    function (): void {
        expect($this->factory->make())->toBeInstanceOf(UpdateNginxConfigData::class);
    });

test('toArray() maps content onto the config key',
    function (): void {
        expect(new UpdateNginxConfigData(content: 'server {}')->toArray())->toBe(['config' => 'server {}']);
    });
