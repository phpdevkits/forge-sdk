<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use Generator;
use PhpDevKits\ForgeSdk\Data\CreateSshKeyData;
use PhpDevKits\ForgeSdk\Data\ListSshKeysOptions;
use PhpDevKits\ForgeSdk\Data\Page;
use PhpDevKits\ForgeSdk\Data\SshKey;
use PhpDevKits\ForgeSdk\Requests\Servers\CreateSshKey;
use PhpDevKits\ForgeSdk\Requests\Servers\GetSshKeys;
use PhpDevKits\ForgeSdk\Resources\Concerns\ParsesPage;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Throwable;

// NOTE: POST /ssh-keys returns 202 Accepted with an empty body — Forge installs
// the key asynchronously on the server. The endpoint does not return the
// newly-created KeyResource (contrary to the spec). Callers wanting the
// created SshKey DTO should call $this->all() / $this->iterate() after create
// and match by name.

final class SshKeysResource extends BaseResource
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
     * @return Page<SshKey>
     *
     * @throws Throwable
     */
    public function all(?ListSshKeysOptions $options = null): Page
    {
        return $this->parsePage(
            $this->connector->send(new GetSshKeys($this->organization, $this->serverId, $options)),
            SshKey::from(...),
        );
    }

    /**
     * @return Generator<int, SshKey>
     *
     * @throws Throwable
     */
    public function iterate(?ListSshKeysOptions $options = null): Generator
    {
        $options ??= new ListSshKeysOptions;

        do {
            $page = $this->all($options);

            yield from $page;

            $options = new ListSshKeysOptions(
                size: $options->size,
                cursor: $page->nextCursor,
                name: $options->name,
                user: $options->user,
            );
        } while ($page->hasMore());
    }

    /**
     * Install a new SSH key on the server.
     *
     * Forge fires the install asynchronously and returns 202 Accepted with an
     * empty body — there's no `KeyResource` to hydrate. To retrieve the
     * created key, call {@see all()} / {@see iterate()} afterwards and match
     * by name.
     *
     * @throws Throwable
     */
    public function create(CreateSshKeyData $data): void
    {
        $this->connector->send(new CreateSshKey($this->organization, $this->serverId, $data));
    }
}
