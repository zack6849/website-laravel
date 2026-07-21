<?php

declare(strict_types=1);

namespace App\Rules;

use App\Enums\BackgroundScheduleRuleType;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Carbon;

class BackgroundSchedule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === []) {
            return;
        }

        if (! is_array($value)) {
            $fail('The background schedule must be a list of rules.');

            return;
        }

        foreach ($value as $index => $rule) {
            if (! is_array($rule)) {
                $fail("Schedule rule {$index} must be an object.");

                continue;
            }

            $type = is_string($rule['type'] ?? null) ? BackgroundScheduleRuleType::tryFrom($rule['type']) : null;

            match ($type) {
                BackgroundScheduleRuleType::Date => $this->validateDateRule($rule, $index, $fail),
                BackgroundScheduleRuleType::Weekday => $this->validateWeekdayRule($rule, $index, $fail),
                BackgroundScheduleRuleType::Range => $this->validateRangeRule($rule, $index, $fail),
                null => $fail("Schedule rule {$index} must have a valid type."),
            };
        }
    }

    private function validateDateRule(array $rule, int|string $index, Closure $fail): void
    {
        $month = filter_var($rule['month'] ?? null, FILTER_VALIDATE_INT);
        $day = filter_var($rule['day'] ?? null, FILTER_VALIDATE_INT);

        if ($month === false || $day === false || ! checkdate($month, $day, 2000)) {
            $fail("Schedule rule {$index} must use a valid month and day.");
        }
    }

    private function validateWeekdayRule(array $rule, int|string $index, Closure $fail): void
    {
        $days = $rule['days'] ?? [];

        if (! is_array($days) || $days === []) {
            $fail("Schedule rule {$index} must choose at least one weekday.");

            return;
        }

        foreach ($days as $day) {
            $day = filter_var($day, FILTER_VALIDATE_INT);

            if ($day === false || $day < 0 || $day > 6) {
                $fail("Schedule rule {$index} contains an invalid weekday.");

                return;
            }
        }
    }

    private function validateRangeRule(array $rule, int|string $index, Closure $fail): void
    {
        $start = $this->parseDate($rule['start'] ?? null);
        $end = $this->parseDate($rule['end'] ?? null);

        if ($start === null || $end === null) {
            $fail("Schedule rule {$index} must include valid start and end dates.");

            return;
        }

        if ($end->lt($start)) {
            $fail("Schedule rule {$index} end date must be on or after the start date.");
        }
    }

    private function parseDate(mixed $date): ?Carbon
    {
        if (! is_string($date) || trim($date) === '') {
            return null;
        }

        try {
            return Carbon::parse($date)->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }
}
