<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use PhpDevKits\ForgeSdk\Data\Organization;
use PhpDevKits\ForgeSdk\Requests\Organizations\GetOrganization;
use RuntimeException;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Throwable;

final class OrganizationResource extends BaseResource
{
    public function __construct(
        Connector $connector,
        private readonly string $slug,
    ) {
        parent::__construct($connector);
    }

    /**
     * @throws Throwable
     */
    public function get(): Organization
    {
        $response = $this->connector->send(new GetOrganization($this->slug));

        $data = $response->json('data');

        if (! is_array($data)) {
            throw new RuntimeException('Forge /orgs/{slug} response did not include a `data` object.');
        }

        return Organization::from($data);
    }
}
