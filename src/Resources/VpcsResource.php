<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use Generator;
use PhpDevKits\ForgeSdk\Data\CreateVpcData;
use PhpDevKits\ForgeSdk\Data\Page;
use PhpDevKits\ForgeSdk\Data\Vpc;
use PhpDevKits\ForgeSdk\Requests\ServerCredentials\CreateVpc;
use PhpDevKits\ForgeSdk\Requests\ServerCredentials\GetVpcs;
use PhpDevKits\ForgeSdk\Resources\Concerns\ParsesPage;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Throwable;

final class VpcsResource extends BaseResource
{
    use ParsesPage;

    public function __construct(
        Connector $connector,
        private readonly string $organization,
        private readonly int|string $credentialId,
        private readonly string $region,
    ) {
        parent::__construct($connector);
    }

    /**
     * @return Page<Vpc>
     *
     * @throws Throwable
     */
    public function all(): Page
    {
        return $this->parsePage(
            $this->connector->send(new GetVpcs($this->organization, $this->credentialId, $this->region)),
            Vpc::from(...),
        );
    }

    /**
     * @return Generator<int, Vpc>
     *
     * @throws Throwable
     */
    public function iterate(): Generator
    {
        do {
            $page = $this->all();

            yield from $page;
        } while ($page->hasMore());
    }

    /**
     * Scope to a single VPC by id within this credential's region.
     */
    public function vpc(int|string $vpcId): VpcResource
    {
        return new VpcResource($this->connector, $this->organization, $this->credentialId, $this->region, $vpcId);
    }

    /**
     * Create a new VPC in this credential's region. Returns the hydrated Vpc DTO.
     *
     * @throws Throwable
     */
    public function create(CreateVpcData $data): Vpc
    {
        $response = $this->connector->send(new CreateVpc($this->organization, $this->credentialId, $this->region, $data));

        /** @var array<array-key, mixed> $body */
        $body = $response->json('data');

        return Vpc::from($body);
    }
}
