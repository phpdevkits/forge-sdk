<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Requests\Servers;

use Override;
use PhpDevKits\ForgeSdk\Enums\DaemonAction;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

final class SendDaemonAction extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        private readonly string $organization,
        private readonly int|string $serverId,
        private readonly int|string $daemonId,
        private readonly DaemonAction $action,
    ) {}

    #[Override]
    public function resolveEndpoint(): string
    {
        return sprintf('/orgs/%s/servers/%s/background-processes/%s/actions', $this->organization, $this->serverId, $this->daemonId);
    }

    /**
     * @return array{action: string}
     */
    protected function defaultBody(): array
    {
        return ['action' => $this->action->value];
    }
}
