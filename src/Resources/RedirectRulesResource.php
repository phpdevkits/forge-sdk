<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use Generator;
use PhpDevKits\ForgeSdk\Data\CreateRedirectRuleData;
use PhpDevKits\ForgeSdk\Data\ListRedirectRulesOptions;
use PhpDevKits\ForgeSdk\Data\Page;
use PhpDevKits\ForgeSdk\Data\RedirectRule;
use PhpDevKits\ForgeSdk\Requests\Sites\CreateRedirectRule;
use PhpDevKits\ForgeSdk\Requests\Sites\GetRedirectRules;
use PhpDevKits\ForgeSdk\Resources\Concerns\ParsesPage;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Throwable;

// NOTE: POST /redirect-rules returns 202 Accepted with an empty body — Forge
// installs the rule asynchronously on the server. The endpoint does not return
// the newly-created RedirectRuleResource. Callers wanting the created
// RedirectRule DTO should call $this->all() / $this->iterate() after create
// and match by `from`/`to`.

final class RedirectRulesResource extends BaseResource
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
     * @return Page<RedirectRule>
     *
     * @throws Throwable
     */
    public function all(?ListRedirectRulesOptions $options = null): Page
    {
        return $this->parsePage(
            $this->connector->send(new GetRedirectRules($this->organization, $this->serverId, $this->siteId, $options)),
            RedirectRule::from(...),
        );
    }

    /**
     * @return Generator<int, RedirectRule>
     *
     * @throws Throwable
     */
    public function iterate(?ListRedirectRulesOptions $options = null): Generator
    {
        $options ??= new ListRedirectRulesOptions;

        do {
            $page = $this->all($options);

            yield from $page;

            $options = new ListRedirectRulesOptions(
                size: $options->size,
                cursor: $page->nextCursor,
                sort: $options->sort,
                from: $options->from,
                to: $options->to,
                type: $options->type,
                status: $options->status,
            );
        } while ($page->hasMore());
    }

    /**
     * Add a new redirect rule to the site.
     *
     * Forge fires the install asynchronously and returns 202 Accepted with an
     * empty body — there's no `RedirectRuleResource` to hydrate. To retrieve
     * the created rule, call {@see all()} / {@see iterate()} afterwards and
     * match by `from`/`to`.
     *
     * @throws Throwable
     */
    public function create(CreateRedirectRuleData $data): void
    {
        $this->connector->send(new CreateRedirectRule($this->organization, $this->serverId, $this->siteId, $data));
    }
}
