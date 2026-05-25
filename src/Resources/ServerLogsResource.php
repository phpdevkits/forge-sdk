<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use PhpDevKits\ForgeSdk\Requests\Servers\DeleteServerLog;
use PhpDevKits\ForgeSdk\Requests\Servers\GetServerLog;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Throwable;

// NOTE: server logs are raw-content fetches keyed by a string identifier (the
// `key` path segment). Forge has no "list available logs" endpoint — callers
// must already know the key. The GET response is JSON:API; the content lives
// under `data.attributes.content`. DELETE empties the log async (202).
final class ServerLogsResource extends BaseResource
{
    public function __construct(
        Connector $connector,
        private readonly string $organization,
        private readonly int|string $serverId,
    ) {
        parent::__construct($connector);
    }

    /**
     * Return the raw content of the server log identified by $key.
     *
     * @throws Throwable
     */
    public function get(string $key): string
    {
        $response = $this->connector->send(new GetServerLog($this->organization, $this->serverId, $key));

        $content = $response->json('data.attributes.content');

        return is_string($content) ? $content : '';
    }

    /**
     * Empty the server log identified by $key. Forge processes the delete
     * asynchronously and returns an empty body.
     *
     * @throws Throwable
     */
    public function delete(string $key): void
    {
        $this->connector->send(new DeleteServerLog($this->organization, $this->serverId, $key));
    }
}
