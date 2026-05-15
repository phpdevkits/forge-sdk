<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use Generator;
use PhpDevKits\ForgeSdk\Data\CreateServerData;
use PhpDevKits\ForgeSdk\Data\ListServersOptions;
use PhpDevKits\ForgeSdk\Data\Page;
use PhpDevKits\ForgeSdk\Data\Server;
use PhpDevKits\ForgeSdk\Requests\Servers\CreateServer;
use PhpDevKits\ForgeSdk\Requests\Servers\GetServers;
use PhpDevKits\ForgeSdk\Resources\Concerns\ParsesPage;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Throwable;

final class ServersResource extends BaseResource
{
    use ParsesPage;

    public function __construct(
        Connector $connector,
        private readonly string $organization,
    ) {
        parent::__construct($connector);
    }

    /**
     * @return Page<Server>
     *
     * @throws Throwable
     */
    public function all(?ListServersOptions $options = null): Page
    {
        return $this->parsePage(
            $this->connector->send(new GetServers($this->organization, $options)),
            Server::from(...),
        );
    }

    /**
     * Create a new server. Returns the hydrated Server DTO from the response.
     *
     * @throws Throwable
     */
    public function create(CreateServerData $data): Server
    {
        $response = $this->connector->send(new CreateServer($this->organization, $data));

        /** @var array<array-key, mixed> $body */
        $body = $response->json('data');

        return Server::from($body);
    }

    /**
     * @return Generator<int, Server>
     *
     * @throws Throwable
     */
    public function iterate(?ListServersOptions $options = null): Generator
    {
        $options ??= new ListServersOptions;

        do {
            $page = $this->all($options);

            yield from $page;

            $options = new ListServersOptions(
                size: $options->size,
                cursor: $page->nextCursor,
                sort: $options->sort,
                ipAddress: $options->ipAddress,
                name: $options->name,
                region: $options->region,
                sizeFilter: $options->sizeFilter,
                provider: $options->provider,
                ubuntuVersion: $options->ubuntuVersion,
                phpVersion: $options->phpVersion,
                databaseType: $options->databaseType,
            );
        } while ($page->hasMore());
    }
}
