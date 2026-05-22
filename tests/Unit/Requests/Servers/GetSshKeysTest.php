<?php

declare(strict_types=1);

namespace Tests\Unit\Requests\Servers;

use PhpDevKits\ForgeSdk\Data\ListSshKeysOptions;
use PhpDevKits\ForgeSdk\Requests\Servers\GetSshKeys;
use Saloon\Enums\Method;

test('resolveEndpoint() interpolates the org slug and server id',
    function (): void {
        $request = new GetSshKeys('acme', 42);

        expect($request->resolveEndpoint())->toBe('/orgs/acme/servers/42/ssh-keys');
    });

test('uses the GET method',
    function (): void {
        expect(new GetSshKeys('acme', 42)->getMethod())->toBe(Method::GET);
    });

test('emits options as query parameters when supplied',
    function (): void {
        $request = new GetSshKeys('acme', 42, new ListSshKeysOptions(size: 5, name: 'oliver'));

        expect($request->query()->all())->toBe([
            'page[size]' => 5,
            'filter[name]' => 'oliver',
        ]);
    });
