<?php

declare(strict_types=1);

namespace App\Validator;

final class PreferenceValidator
{
    private const ALLOWED_PREFERENCES = [
        'theme' => ['dark', 'light'],
        'display' => ['list', 'grid'],
        'shell' => ['bash', 'lshell'],
        'lang' => ['en', 'fr'],
    ];

    public function validate(string $preferenceKey, $value): void
    {
        if (!array_key_exists($preferenceKey, self::ALLOWED_PREFERENCES)) {
            throw new \InvalidArgumentException(sprintf('Invalid preference key: %s', $preferenceKey));
        }

        if ($value === null) {
            throw new \InvalidArgumentException(sprintf('Missing value for preference key: %s', $preferenceKey));
        }

        if (!$this->isAllowedValue($preferenceKey, $value)) {
            throw new \InvalidArgumentException(sprintf('Invalid value for %s', $preferenceKey));
        }
    }

    public function isAllowedValue(string $preferenceKey, mixed $value): bool
    {
        return in_array($value, self::ALLOWED_PREFERENCES[$preferenceKey], true);
    }
}
