<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use PhpDevKits\ForgeSdk\Data\ScheduledJob;
use PhpDevKits\ForgeSdk\Requests\Sites\DeleteScheduledJob;
use PhpDevKits\ForgeSdk\Requests\Sites\GetScheduledJob;
use PhpDevKits\ForgeSdk\Requests\Sites\GetScheduledJobOutput;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Throwable;

final class SiteScheduledJobResource extends BaseResource
{
    public function __construct(
        Connector $connector,
        private readonly string $organization,
        private readonly int|string $serverId,
        private readonly int|string $siteId,
        private readonly int|string $jobId,
    ) {
        parent::__construct($connector);
    }

    /**
     * @throws Throwable
     */
    public function get(): ScheduledJob
    {
        $response = $this->connector->send(new GetScheduledJob($this->organization, $this->serverId, $this->siteId, $this->jobId));

        /** @var array<array-key, mixed> $data */
        $data = $response->json('data');

        return ScheduledJob::from($data);
    }

    /**
     * @throws Throwable
     */
    public function delete(): void
    {
        $this->connector->send(new DeleteScheduledJob($this->organization, $this->serverId, $this->siteId, $this->jobId));
    }

    /**
     * Return the raw captured output of the scheduled job's last run.
     *
     * @throws Throwable
     */
    public function output(): string
    {
        $response = $this->connector->send(new GetScheduledJobOutput($this->organization, $this->serverId, $this->siteId, $this->jobId));

        $output = $response->json('data.attributes.output');

        return is_string($output) ? $output : '';
    }
}
