<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

use InvalidArgumentException;
use JsonSerializable;
use Override;

final readonly class ProviderRegion implements JsonSerializable
{
    public function __construct(
        public string $id,
        public string $name,
        public string $code,
        public ?string $alternateCode,
    ) {}

    /**
     * @param  array<array-key, mixed>  $data  A JSON:API `ProviderRegionResource` object.
     */
    public static function from(array $data): self
    {
        $id = $data['id'] ?? null;
        if (! is_string($id)) {
            throw new InvalidArgumentException('ProviderRegion data is missing the `id` field.');
        }

        $attributes = $data['attributes'] ?? null;
        if (! is_array($attributes)) {
            throw new InvalidArgumentException('ProviderRegion data is missing the `attributes` object.');
        }

        $name = $attributes['name'] ?? null;
        if (! is_string($name)) {
            throw new InvalidArgumentException('ProviderRegion `attributes.name` must be a string.');
        }

        $code = $attributes['code'] ?? null;
        if (! is_string($code)) {
            throw new InvalidArgumentException('ProviderRegion `attributes.code` must be a string.');
        }

        $alternateCode = $attributes['alternate_code'] ?? null;
        if ($alternateCode !== null && ! is_string($alternateCode)) {
            throw new InvalidArgumentException('ProviderRegion `attributes.alternate_code` must be a string or null.');
        }

        return new self(
            id: $id,
            name: $name,
            code: $code,
            alternateCode: $alternateCode,
        );
    }

    /**
     * @return array{id: string, name: string, code: string, alternate_code: ?string}
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'alternate_code' => $this->alternateCode,
        ];
    }
}
