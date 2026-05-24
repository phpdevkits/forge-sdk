<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use PhpDevKits\ForgeSdk\Data\ServerCredential;
use PhpDevKits\ForgeSdk\Requests\ServerCredentials\GetServerCredential;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Throwable;

final class ServerCredentialResource extends BaseResource
{
    public function __construct(
        Connector $connector,
        private readonly string $organization,
        private readonly int|string $id,
    ) {
        parent::__construct($connector);
    }

    /**
     * @throws Throwable
     */
    public function get(): ServerCredential
    {
        $response = $this->connector->send(new GetServerCredential($this->organization, $this->id));

        /** @var array<array-key, mixed> $data */
        $data = $response->json('data');

        return ServerCredential::from($data);
    }

    /**
     * Scope the VPC sub-resources to a provider region for this credential.
     */
    public function region(string $region): VpcsResource
    {
        return new VpcsResource($this->connector, $this->organization, $this->id, $region);
    }
}
