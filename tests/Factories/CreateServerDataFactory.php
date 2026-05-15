<?php

declare(strict_types=1);

namespace Tests\Factories;

use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\CreateServerData;
use PhpDevKits\ForgeSdk\Enums\PhpVersion;
use PhpDevKits\ForgeSdk\Enums\ServerType;
use PhpDevKits\ForgeSdk\Enums\UbuntuVersion;

/**
 * @extends Factory<CreateServerData>
 */
final class CreateServerDataFactory extends Factory
{
    /** @var class-string<CreateServerData> */
    protected string $dataObject = CreateServerData::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'name' => $this->fake->bothify('test-server-##'),
            'provider' => 'hetzner',
            'type' => ServerType::App,
            'ubuntuVersion' => UbuntuVersion::Ubuntu2404,
            'credentialId' => $this->fake->numberBetween(1, 9_999),
            'phpVersion' => PhpVersion::Php84,
            'databaseType' => null,
            'hetzner' => null,
        ];
    }
}
