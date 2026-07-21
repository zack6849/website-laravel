<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * The set of valid `type` values for a background schedule rule, shared by
 * the admin form (BackgroundFormData), the validation rule (BackgroundSchedule),
 * and the runtime matcher (BackgroundScheduleMatcher) so the three stay in sync.
 */
enum BackgroundScheduleRuleType: string
{
    case Date = 'date';
    case Weekday = 'weekday';
    case Range = 'range';
}
