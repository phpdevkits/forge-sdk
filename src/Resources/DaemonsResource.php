<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use Generator;
use PhpDevKits\ForgeSdk\Data\CreateDaemonData;
use PhpDevKits\ForgeSdk\Data\Daemon;
use PhpDevKits\ForgeSdk\Data\ListDaemonsOptions;
use PhpDevKits\ForgeSdk\Data\Page;
use PhpDevKits\ForgeSdk\Requests\Servers\CreateDaemon;
use PhpDevKits\ForgeSdk\Requests\Servers\GetDaemons;
use PhpDevKits\ForgeSdk\Resources\Concerns\ParsesPage;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Throwable;

final class DaemonsResource extends BaseResource
{
    use ParsesPage;

    public function __construct(
        Connector $connector,
        private readonly string $organization,
        private readonly int|string $serverId,
    ) {
        parent::__construct($connector);
    }

    /**
     * @return Page<Daemon>
     *
     * @throws Throwable
     */
    public function all(?ListDaemonsOptions $options = null): Page
    {
        return $this->parsePage(
            $this->connector->send(new GetDaemons($this->organization, $this->serverId, $options)),
            Daemon::from(...),
        );
    }

    /**
     * @return Generator<int, Daemon>
     *
     * @throws Throwable
     */
    public function iterate(?ListDaemonsOptions $options = null): Generator
    {
        $options ??= new ListDaemonsOptions;

        do {
            $page = $this->all($options);

            yield from $page;

            $options = new ListDaemonsOptions(
                size: $options->size,
                cursor: $page->nextCursor,
                sort: $options->sort,
                user: $options->user,
                siteId: $options->siteId,
                directory: $options->directory,
            );
        } while ($page->hasMore());
    }

    /**
     * @throws Throwable
     */
    public function create(CreateDaemonData $data): Daemon
    {
        $response = $this->connector->send(new CreateDaemon($this->organization, $this->serverId, $data));

        /** @var array<array-key, mixed> $body */
        $body = $response->json('data');

        return Daemon::from($body);
    }
}
