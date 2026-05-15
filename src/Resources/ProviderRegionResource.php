<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use PhpDevKits\ForgeSdk\Data\ProviderRegion;
use PhpDevKits\ForgeSdk\Requests\Providers\GetProviderRegion;
use RuntimeException;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Throwable;

final class ProviderRegionResource extends BaseResource
{
    public function __construct(
        Connector $connector,
        private readonly int|string $providerId,
        private readonly int|string $regionId,
    ) {
        parent::__construct($connector);
    }

    /**
     * @throws Throwable
     */
    public function get(): ProviderRegion
    {
        $response = $this->connector->send(new GetProviderRegion($this->providerId, $this->regionId));

        $data = $response->json('data');

        if (! is_array($data)) {
            throw new RuntimeException('Forge /providers/{slug}/regions/{region} response did not include a `data` object.');
        }

        return ProviderRegion::from($data);
    }

    public function sizes(): ProviderRegionSizesResource
    {
        return new ProviderRegionSizesResource($this->connector, $this->providerId, $this->regionId);
    }

    public function size(int|string $sizeId): ProviderRegionSizeResource
    {
        return new ProviderRegionSizeResource($this->connector, $this->providerId, $this->regionId, $sizeId);
    }
}
