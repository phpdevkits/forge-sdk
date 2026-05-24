<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Enums;

/**
 * Frequency presets a scheduled job can run on.
 *
 * Mirrors the `frequency` enum on Forge's CreateScheduledJobRequest.
 * Use {@see CronFrequency::Custom} together with a raw `cron` expression.
 */
enum CronFrequency: string
{
    case Minutely = 'minutely';
    case Hourly = 'hourly';
    case Nightly = 'nightly';
    case Weekly = 'weekly';
    case Monthly = 'monthly';
    case Reboot = 'reboot';
    case Custom = 'custom';
}
