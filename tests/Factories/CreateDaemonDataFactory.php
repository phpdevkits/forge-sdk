<?php

declare(strict_types=1);

namespace Tests\Factories;

use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\CreateDaemonData;
use PhpDevKits\ForgeSdk\Enums\DaemonUser;

/**
 * @extends Factory<CreateDaemonData>
 */
final class CreateDaemonDataFactory extends Factory
{
    /** @var class-string<CreateDaemonData> */
    protected string $dataObject = CreateDaemonData::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'name' => $this->fake->bothify('worker-##'),
            'command' => 'sleep 9999',
            'user' => DaemonUser::Forge,
            'processes' => 1,
            'siteId' => null,
            'directory' => null,
            'startSecs' => null,
            'stopWaitSecs' => null,
            'stopSignal' => null,
        ];
    }
}
