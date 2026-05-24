<?php

declare(strict_types=1);

namespace Tests\Unit\Data;

use PhpDevKits\ForgeSdk\Data\CreateVpcData;
use Tests\Factories\CreateVpcDataFactory;

beforeEach(function (): void {
    $this->factory = new CreateVpcDataFactory;
});

test('factory produces a usable CreateVpcData',
    function (): void {
        $data = $this->factory->make();

        expect($data)->toBeInstanceOf(CreateVpcData::class)
            ->and($data->name)->toBeString()->not->toBeEmpty();
    });

test('toArray() emits the name field',
    function (): void {
        expect(new CreateVpcData(name: 'my-vpc')->toArray())->toBe([
            'name' => 'my-vpc',
        ]);
    });
