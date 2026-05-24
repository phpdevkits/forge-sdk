<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use Generator;
use PhpDevKits\ForgeSdk\Data\ListServerCredentialsOptions;
use PhpDevKits\ForgeSdk\Data\Page;
use PhpDevKits\ForgeSdk\Data\ServerCredential;
use PhpDevKits\ForgeSdk\Requests\ServerCredentials\GetServerCredentials;
use PhpDevKits\ForgeSdk\Resources\Concerns\ParsesPage;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Throwable;

final class ServerCredentialsResource extends BaseResource
{
    use ParsesPage;

    public function __construct(
        Connector $connector,
        private readonly string $organization,
    ) {
        parent::__construct($connector);
    }

    /**
     * @return Page<ServerCredential>
     *
     * @throws Throwable
     */
    public function all(?ListServerCredentialsOptions $options = null): Page
    {
        return $this->parsePage(
            $this->connector->send(new GetServerCredentials($this->organization, $options)),
            ServerCredential::from(...),
        );
    }

    /**
     * @return Generator<int, ServerCredential>
     *
     * @throws Throwable
     */
    public function iterate(?ListServerCredentialsOptions $options = null): Generator
    {
        $options ??= new ListServerCredentialsOptions;

        do {
            $page = $this->all($options);

            yield from $page;

            $options = new ListServerCredentialsOptions(
                size: $options->size,
                cursor: $page->nextCursor,
            );
        } while ($page->hasMore());
    }
}
