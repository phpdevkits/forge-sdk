<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use InvalidArgumentException;
use PhpDevKits\ForgeSdk\Data\DeploymentScript;
use Tests\Factories\DeploymentScriptFactory;

beforeEach(function (): void {
    $this->factory = new DeploymentScriptFactory;
});

test('factory produces a usable DeploymentScript',
    function (): void {
        expect($this->factory->make())->toBeInstanceOf(DeploymentScript::class);
    });

test('::from() hydrates content + auto_source',
    function (): void {
        $script = DeploymentScript::from([
            'id' => '7',
            'type' => 'deploymentScripts',
            'attributes' => ['content' => 'git pull', 'auto_source' => true],
        ]);

        expect($script->id)->toBe('7')
            ->and($script->content)->toBe('git pull')
            ->and($script->autoSource)->toBeTrue();
    });

test('::from() accepts null content',
    function (): void {
        $script = DeploymentScript::from(['id' => '1', 'attributes' => ['content' => null, 'auto_source' => false]]);

        expect($script->content)->toBeNull();
    });

test('::from() throws when id is missing',
    function (): void {
        DeploymentScript::from(['attributes' => ['content' => null, 'auto_source' => false]]);
    })->throws(InvalidArgumentException::class, 'missing the `id` field');

test('::from() throws when attributes is missing',
    function (): void {
        DeploymentScript::from(['id' => '1']);
    })->throws(InvalidArgumentException::class, 'missing the `attributes` object');

test('::from() throws when content is the wrong type',
    function (): void {
        DeploymentScript::from(['id' => '1', 'attributes' => ['content' => 42, 'auto_source' => false]]);
    })->throws(InvalidArgumentException::class, '`attributes.content` must be a string or null');

test('::from() throws when auto_source is missing',
    function (): void {
        DeploymentScript::from(['id' => '1', 'attributes' => ['content' => null]]);
    })->throws(InvalidArgumentException::class, '`attributes.auto_source` must be a boolean');

test('jsonSerialize() emits snake_case auto_source',
    function (): void {
        expect(new DeploymentScript(id: '1', content: 'x', autoSource: true)->jsonSerialize())->toBe([
            'id' => '1',
            'content' => 'x',
            'auto_source' => true,
        ]);
    });
