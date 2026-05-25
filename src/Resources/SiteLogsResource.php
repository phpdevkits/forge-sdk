<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Resources;

use PhpDevKits\ForgeSdk\Enums\SiteLog;
use PhpDevKits\ForgeSdk\Requests\Servers\DeleteSiteLog;
use PhpDevKits\ForgeSdk\Requests\Servers\GetSiteLog;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Throwable;

// NOTE: a site exposes exactly two Nginx logs — access and error — at fixed
// path segments (see SiteLog). Both are raw-content fetches; the JSON:API GET
// response carries the content under `data.attributes.content`. DELETE empties
// the log async (202).
final class SiteLogsResource extends BaseResource
{
    public function __construct(
        Connector $connector,
        private readonly string $organization,
        private readonly int|string $serverId,
        private readonly int|string $siteId,
    ) {
        parent::__construct($connector);
    }

    /**
     * Return the raw Nginx access log content for the site.
     *
     * @throws Throwable
     */
    public function nginxAccess(): string
    {
        return $this->content(SiteLog::NginxAccess);
    }

    /**
     * Return the raw Nginx error log content for the site.
     *
     * @throws Throwable
     */
    public function nginxError(): string
    {
        return $this->content(SiteLog::NginxError);
    }

    /**
     * Empty the site's Nginx access log. Forge processes the delete
     * asynchronously and returns an empty body.
     *
     * @throws Throwable
     */
    public function deleteNginxAccess(): void
    {
        $this->connector->send(new DeleteSiteLog($this->organization, $this->serverId, $this->siteId, SiteLog::NginxAccess));
    }

    /**
     * Empty the site's Nginx error log. Forge processes the delete
     * asynchronously and returns an empty body.
     *
     * @throws Throwable
     */
    public function deleteNginxError(): void
    {
        $this->connector->send(new DeleteSiteLog($this->organization, $this->serverId, $this->siteId, SiteLog::NginxError));
    }

    /**
     * @throws Throwable
     */
    private function content(SiteLog $log): string
    {
        $response = $this->connector->send(new GetSiteLog($this->organization, $this->serverId, $this->siteId, $log));

        $content = $response->json('data.attributes.content');

        return is_string($content) ? $content : '';
    }
}
