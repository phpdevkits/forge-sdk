<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use PhpDevKits\ForgeSdk\Data\Vpc;
use PhpDevKits\ForgeSdk\Requests\ServerCredentials\GetVpc;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Throwable;

final class VpcResource extends BaseResource
{
    public function __construct(
        Connector $connector,
        private readonly string $organization,
        private readonly int|string $credentialId,
        private readonly string $region,
        private readonly int|string $vpcId,
    ) {
        parent::__construct($connector);
    }

    /**
     * @throws Throwable
     */
    public function get(): Vpc
    {
        $response = $this->connector->send(new GetVpc($this->organization, $this->credentialId, $this->region, $this->vpcId));

        /** @var array<array-key, mixed> $data */
        $data = $response->json('data');

        return Vpc::from($data);
    }
}
