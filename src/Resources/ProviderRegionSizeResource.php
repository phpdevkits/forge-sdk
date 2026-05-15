<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use PhpDevKits\ForgeSdk\Data\ProviderSize;
use PhpDevKits\ForgeSdk\Requests\Providers\GetProviderRegionSize;
use RuntimeException;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Throwable;

final class ProviderRegionSizeResource extends BaseResource
{
    public function __construct(
        Connector $connector,
        private readonly int|string $providerId,
        private readonly int|string $regionId,
        private readonly int|string $sizeId,
    ) {
        parent::__construct($connector);
    }

    /**
     * @throws Throwable
     */
    public function get(): ProviderSize
    {
        $response = $this->connector->send(new GetProviderRegionSize($this->providerId, $this->regionId, $this->sizeId));

        $data = $response->json('data');

        if (! is_array($data)) {
            throw new RuntimeException('Forge /providers/{slug}/regions/{region}/sizes/{size} response did not include a `data` object.');
        }

        return ProviderSize::from($data);
    }
}
