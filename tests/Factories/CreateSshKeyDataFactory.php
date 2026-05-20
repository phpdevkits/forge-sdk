<?php

declare(strict_types=1);

namespace Tests\Factories;

use FBarrento\DataFactory\Factory;
use Override;
use PhpDevKits\ForgeSdk\Data\CreateSshKeyData;

/**
 * @extends Factory<CreateSshKeyData>
 */
final class CreateSshKeyDataFactory extends Factory
{
    /** @var class-string<CreateSshKeyData> */
    protected string $dataObject = CreateSshKeyData::class;

    /**
     * @return array<string, mixed>
     */
    #[Override]
    public function definition(): array
    {
        return [
            'name' => $this->fake->bothify('sdk-test-?#'),
            // Throwaway ed25519 public key — its matching private key was
            // discarded at generation time and never trusted anywhere.
            'key' => 'ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIEXg6jOKTFqqT8itfVL8PIVFycrvBMsohv/d1t+V+86e sdk-test@example.com',
            'user' => null,
        ];
    }
}
