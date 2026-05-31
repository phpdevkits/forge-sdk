<?php

declare(strict_types=1);

namespace PhpDevKits\ForgeSdk\Enums;

/**
 * Frequency presets a backup configuration can run on.
 *
 * Mirrors the `BackupFrequency` enum on Forge's
 * CreateBackupConfigurationRequest / UpdateBackupConfigurationRequest. Use
 * {@see BackupFrequency::Custom} together with a raw `cron` expression.
 */
enum BackupFrequency: string
{
    case Hourly = 'hourly';
    case Daily = 'daily';
    case Weekly = 'weekly';
    case Custom = 'custom';
}
