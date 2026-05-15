<?php

declare(strict_types=1);

namespace Tests\Unit\Exceptions;

use PhpDevKits\ForgeSdk\Exceptions\ForgeException;
use PhpDevKits\ForgeSdk\Exceptions\OrganizationNotSetException;

test('forAccessor() produces a ForgeException with a guidance message',
    function (): void {
        $exception = OrganizationNotSetException::forAccessor('servers');

        expect($exception)->toBeInstanceOf(ForgeException::class)
            ->and($exception->getMessage())->toContain('Forge::servers')
            ->and($exception->getMessage())->toContain('$forge->org($slug)');
    });
