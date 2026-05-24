<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use Generator;
use PhpDevKits\ForgeSdk\Data\CreateScheduledJobData;
use PhpDevKits\ForgeSdk\Data\ListScheduledJobsOptions;
use PhpDevKits\ForgeSdk\Data\Page;
use PhpDevKits\ForgeSdk\Data\ScheduledJob;
use PhpDevKits\ForgeSdk\Requests\Servers\CreateScheduledJob;
use PhpDevKits\ForgeSdk\Requests\Servers\GetScheduledJobs;
use PhpDevKits\ForgeSdk\Resources\Concerns\ParsesPage;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Throwable;

final class ScheduledJobsResource extends BaseResource
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
     * @return Page<ScheduledJob>
     *
     * @throws Throwable
     */
    public function all(?ListScheduledJobsOptions $options = null): Page
    {
        return $this->parsePage(
            $this->connector->send(new GetScheduledJobs($this->organization, $this->serverId, $options)),
            ScheduledJob::from(...),
        );
    }

    /**
     * @return Generator<int, ScheduledJob>
     *
     * @throws Throwable
     */
    public function iterate(?ListScheduledJobsOptions $options = null): Generator
    {
        $options ??= new ListScheduledJobsOptions;

        do {
            $page = $this->all($options);

            yield from $page;

            $options = new ListScheduledJobsOptions(
                size: $options->size,
                cursor: $page->nextCursor,
                sort: $options->sort,
                status: $options->status,
                user: $options->user,
            );
        } while ($page->hasMore());
    }

    /**
     * @throws Throwable
     */
    public function create(CreateScheduledJobData $data): ScheduledJob
    {
        $response = $this->connector->send(new CreateScheduledJob($this->organization, $this->serverId, $data));

        /** @var array<array-key, mixed> $body */
        $body = $response->json('data');

        return ScheduledJob::from($body);
    }
}
