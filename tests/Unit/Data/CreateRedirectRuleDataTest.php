<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use PhpDevKits\ForgeSdk\Data\CreateRedirectRuleData;
use PhpDevKits\ForgeSdk\Enums\RedirectType;
use Tests\Factories\CreateRedirectRuleDataFactory;

beforeEach(function (): void {
    $this->factory = new CreateRedirectRuleDataFactory;
});

test('factory produces a usable CreateRedirectRuleData',
    function (): void {
        $data = $this->factory->make();

        expect($data)->toBeInstanceOf(CreateRedirectRuleData::class)
            ->and($data->from)->toBeString()->not->toBeEmpty()
            ->and($data->to)->toBeString()->not->toBeEmpty()
            ->and($data->type)->toBeInstanceOf(RedirectType::class);
    });

test('toArray() emits all three required fields with the enum value',
    function (): void {
        $data = new CreateRedirectRuleData(from: '/old-path', to: '/new-path', type: RedirectType::Permanent);

        expect($data->toArray())->toBe([
            'from' => '/old-path',
            'to' => '/new-path',
            'type' => 'permanent',
        ]);
    });

test('toArray() serializes the redirect type',
    function (): void {
        $data = new CreateRedirectRuleData(from: '/a', to: '/b', type: RedirectType::Redirect);

        expect($data->toArray()['type'])->toBe('redirect');
    });
