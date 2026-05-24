<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use InvalidArgumentException;
use PhpDevKits\ForgeSdk\Data\NginxConfig;
use Tests\Factories\NginxConfigFactory;

beforeEach(function (): void {
    $this->factory = new NginxConfigFactory;
});

test('factory produces a usable NginxConfig',
    function (): void {
        expect($this->factory->make())->toBeInstanceOf(NginxConfig::class);
    });

test('::from() hydrates id + content',
    function (): void {
        $config = NginxConfig::from([
            'id' => '7',
            'type' => 'nginxConfigs',
            'attributes' => ['content' => 'server {}'],
        ]);

        expect($config->id)->toBe('7')
            ->and($config->content)->toBe('server {}');
    });

test('::from() accepts null content',
    function (): void {
        $config = NginxConfig::from(['id' => '1', 'attributes' => ['content' => null]]);

        expect($config->content)->toBeNull();
    });

test('::from() throws when id is missing',
    function (): void {
        NginxConfig::from(['attributes' => ['content' => null]]);
    })->throws(InvalidArgumentException::class, 'missing the `id` field');

test('::from() throws when attributes is missing',
    function (): void {
        NginxConfig::from(['id' => '1']);
    })->throws(InvalidArgumentException::class, 'missing the `attributes` object');

test('::from() throws when content is the wrong type',
    function (): void {
        NginxConfig::from(['id' => '1', 'attributes' => ['content' => 42]]);
    })->throws(InvalidArgumentException::class, '`attributes.content` must be a string or null');

test('jsonSerialize() emits id + content',
    function (): void {
        expect(new NginxConfig(id: '1', content: 'server {}')->jsonSerialize())->toBe([
            'id' => '1',
            'content' => 'server {}',
        ]);
    });
