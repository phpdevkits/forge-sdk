<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use PhpDevKits\ForgeSdk\Data\Provider;
use PhpDevKits\ForgeSdk\Requests\Providers\GetProvider;
use RuntimeException;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Throwable;

final class ProviderResource extends BaseResource
{
    public function __construct(
        Connector $connector,
        private readonly int|string $id,
    ) {
        parent::__construct($connector);
    }

    /**
     * @throws Throwable
     */
    public function get(): Provider
    {
        $response = $this->connector->send(new GetProvider($this->id));

        $data = $response->json('data');

        if (! is_array($data)) {
            throw new RuntimeException('Forge /providers/{slug} response did not include a `data` object.');
        }

        return Provider::from($data);
    }

    public function regions(): ProviderRegionsResource
    {
        return new ProviderRegionsResource($this->connector, $this->id);
    }

    public function region(int|string $regionId): ProviderRegionResource
    {
        return new ProviderRegionResource($this->connector, $this->id, $regionId);
    }

    public function sizes(): ProviderSizesResource
    {
        return new ProviderSizesResource($this->connector, $this->id);
    }

    public function size(int|string $sizeId): ProviderSizeResource
    {
        return new ProviderSizeResource($this->connector, $this->id, $sizeId);
    }
}
