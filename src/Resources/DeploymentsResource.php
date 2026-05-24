<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use Generator;
use PhpDevKits\ForgeSdk\Data\Deployment;
use PhpDevKits\ForgeSdk\Data\ListDeploymentsOptions;
use PhpDevKits\ForgeSdk\Data\Page;
use PhpDevKits\ForgeSdk\Requests\Servers\GetDeployments;
use PhpDevKits\ForgeSdk\Requests\Servers\TriggerDeployment;
use PhpDevKits\ForgeSdk\Resources\Concerns\ParsesPage;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Throwable;

final class DeploymentsResource extends BaseResource
{
    use ParsesPage;

    public function __construct(
        Connector $connector,
        private readonly string $organization,
        private readonly int|string $serverId,
        private readonly int|string $siteId,
    ) {
        parent::__construct($connector);
    }

    /**
     * @return Page<Deployment>
     *
     * @throws Throwable
     */
    public function all(?ListDeploymentsOptions $options = null): Page
    {
        return $this->parsePage(
            $this->connector->send(new GetDeployments($this->organization, $this->serverId, $this->siteId, $options)),
            Deployment::from(...),
        );
    }

    /**
     * @return Generator<int, Deployment>
     *
     * @throws Throwable
     */
    public function iterate(?ListDeploymentsOptions $options = null): Generator
    {
        $options ??= new ListDeploymentsOptions;

        do {
            $page = $this->all($options);

            yield from $page;

            $options = new ListDeploymentsOptions(
                size: $options->size,
                cursor: $page->nextCursor,
                sort: $options->sort,
                commitHash: $options->commitHash,
                commitMessage: $options->commitMessage,
                commitAuthor: $options->commitAuthor,
            );
        } while ($page->hasMore());
    }

    /**
     * Trigger a new deployment. Returns the hydrated Deployment from the 202
     * response body.
     *
     * @throws Throwable
     */
    public function trigger(): Deployment
    {
        $response = $this->connector->send(new TriggerDeployment($this->organization, $this->serverId, $this->siteId));

        /** @var array<array-key, mixed> $body */
        $body = $response->json('data');

        return Deployment::from($body);
    }
}
