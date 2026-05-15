<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Data;

final readonly class ListServersOptions
{
    public function __construct(
        public ?int $size = null,
        public ?string $cursor = null,
        public ?string $sort = null,
        public ?string $ipAddress = null,
        public ?string $name = null,
        public ?string $region = null,
        public ?string $sizeFilter = null,
        public ?string $provider = null,
        public ?string $ubuntuVersion = null,
        public ?string $phpVersion = null,
        public ?string $databaseType = null,
    ) {}

    /**
     * @return array<string, int|string>
     */
    public function toQuery(): array
    {
        $query = [];

        if ($this->size !== null) {
            $query['page[size]'] = $this->size;
        }

        if ($this->cursor !== null) {
            $query['page[cursor]'] = $this->cursor;
        }

        if ($this->sort !== null) {
            $query['sort'] = $this->sort;
        }

        if ($this->ipAddress !== null) {
            $query['filter[ip_address]'] = $this->ipAddress;
        }

        if ($this->name !== null) {
            $query['filter[name]'] = $this->name;
        }

        if ($this->region !== null) {
            $query['filter[region]'] = $this->region;
        }

        if ($this->sizeFilter !== null) {
            $query['filter[size]'] = $this->sizeFilter;
        }

        if ($this->provider !== null) {
            $query['filter[provider]'] = $this->provider;
        }

        if ($this->ubuntuVersion !== null) {
            $query['filter[ubuntu_version]'] = $this->ubuntuVersion;
        }

        if ($this->phpVersion !== null) {
            $query['filter[php_version]'] = $this->phpVersion;
        }

        if ($this->databaseType !== null) {
            $query['filter[database_type]'] = $this->databaseType;
        }

        return $query;
    }
}
