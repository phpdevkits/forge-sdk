<?php

declare(strict_types=1);

namespace Tests\Factories;

use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\NginxConfig;

/**
 * @extends Factory<NginxConfig>
 */
final class NginxConfigFactory extends Factory
{
    /** @var class-string<NginxConfig> */
    protected string $dataObject = NginxConfig::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'id' => (string) $this->fake->numberBetween(1, 99_999),
            'content' => "server {\n    listen 80;\n    server_name example.com;\n    root /home/forge/example.com/public;\n}",
        ];
    }
}
