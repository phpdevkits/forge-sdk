<?php

declare(strict_types=1);

namespace Tests\Unit\Resources;

use PhpDevKits\ForgeSdk\Forge;
use PhpDevKits\ForgeSdk\Resources\SshKeyResource;
use PhpDevKits\ForgeSdk\Resources\SshKeysResource;

test('server($id)->sshKeys() returns an SshKeysResource',
    function (): void {
        $forge = new Forge('test-token', 'test-org');

        expect($forge->server(42)->sshKeys())->toBeInstanceOf(SshKeysResource::class);
    });

test('server($id)->sshKey($keyId) returns an SshKeyResource',
    function (): void {
        $forge = new Forge('test-token', 'test-org');

        expect($forge->server(42)->sshKey(99))->toBeInstanceOf(SshKeyResource::class);
    });
