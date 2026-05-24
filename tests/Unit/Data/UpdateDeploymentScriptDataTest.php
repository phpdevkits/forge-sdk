<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use PhpDevKits\ForgeSdk\Data\UpdateDeploymentScriptData;
use Tests\Factories\UpdateDeploymentScriptDataFactory;

beforeEach(function (): void {
    $this->factory = new UpdateDeploymentScriptDataFactory;
});

test('factory produces a usable UpdateDeploymentScriptData',
    function (): void {
        expect($this->factory->make())->toBeInstanceOf(UpdateDeploymentScriptData::class);
    });

test('toArray() emits only content when auto_source is null',
    function (): void {
        expect(new UpdateDeploymentScriptData(content: 'git pull')->toArray())->toBe(['content' => 'git pull']);
    });

test('toArray() emits auto_source when set',
    function (): void {
        expect(new UpdateDeploymentScriptData(content: 'git pull', autoSource: true)->toArray())->toBe([
            'content' => 'git pull',
            'auto_source' => true,
        ]);
    });
