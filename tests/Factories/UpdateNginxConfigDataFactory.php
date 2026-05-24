<?php

declare(strict_types=1);

namespace Tests\Factories;

use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\UpdateNginxConfigData;

/**
 * @extends Factory<UpdateNginxConfigData>
 */
final class UpdateNginxConfigDataFactory extends Factory
{
    /** @var class-string<UpdateNginxConfigData> */
    protected string $dataObject = UpdateNginxConfigData::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'content' => "server {\n    listen 80;\n    server_name example.com;\n}",
        ];
    }
}
