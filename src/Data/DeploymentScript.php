<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

use InvalidArgumentException;
use JsonSerializable;
use Override;

/**
 * A site's deployment script. `content` is null when no script has been set.
 */
final readonly class DeploymentScript implements JsonSerializable
{
    public function __construct(
        public string $id,
        public ?string $content,
        public bool $autoSource,
    ) {}

    /**
     * @param  array<array-key, mixed>  $data  A JSON:API `DeploymentScriptResource` object.
     */
    public static function from(array $data): self
    {
        $id = $data['id'] ?? null;
        if (! is_string($id)) {
            throw new InvalidArgumentException('DeploymentScript data is missing the `id` field.');
        }

        $attributes = $data['attributes'] ?? null;
        if (! is_array($attributes)) {
            throw new InvalidArgumentException('DeploymentScript data is missing the `attributes` object.');
        }

        $content = $attributes['content'] ?? null;
        if ($content !== null && ! is_string($content)) {
            throw new InvalidArgumentException('DeploymentScript `attributes.content` must be a string or null.');
        }

        $autoSource = $attributes['auto_source'] ?? null;
        if (! is_bool($autoSource)) {
            throw new InvalidArgumentException('DeploymentScript `attributes.auto_source` must be a boolean.');
        }

        return new self(id: $id, content: $content, autoSource: $autoSource);
    }

    /**
     * @return array{id: string, content: ?string, auto_source: bool}
     */
    #[Override]
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'auto_source' => $this->autoSource,
        ];
    }
}
