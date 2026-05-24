<?php

declare(strict_types=1);

namespace Tests\Factories;

use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\UpdateEnvironmentData;

/**
 * @extends Factory<UpdateEnvironmentData>
 */
final class UpdateEnvironmentDataFactory extends Factory
{
    /** @var class-string<UpdateEnvironmentData> */
    protected string $dataObject = UpdateEnvironmentData::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'content' => "APP_ENV=production\nAPP_DEBUG=false",
            'cache' => null,
            'queues' => null,
            'encryptionKey' => null,
        ];
    }
}
