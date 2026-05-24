<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

use InvalidArgumentException;
use JsonSerializable;
use Override;

/**
 * A site's Nginx configuration. `content` is null when Forge has not yet
 * generated (or is regenerating) the config.
 */
final readonly class NginxConfig implements JsonSerializable
{
    public function __construct(
        public string $id,
        public ?string $content,
    ) {}

    /**
     * @param  array<array-key, mixed>  $data  A JSON:API `NginxConfigResource` object.
     */
    public static function from(array $data): self
    {
        $id = $data['id'] ?? null;
        if (! is_string($id)) {
            throw new InvalidArgumentException('NginxConfig data is missing the `id` field.');
        }

        $attributes = $data['attributes'] ?? null;
        if (! is_array($attributes)) {
            throw new InvalidArgumentException('NginxConfig data is missing the `attributes` object.');
        }

        $content = $attributes['content'] ?? null;
        if ($content !== null && ! is_string($content)) {
            throw new InvalidArgumentException('NginxConfig `attributes.content` must be a string or null.');
        }

        return new self(id: $id, content: $content);
    }

    /**
     * @return array{id: string, content: ?string}
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
        ];
    }
}
