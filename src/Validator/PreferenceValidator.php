<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\HttpFoundation\Request;

final class PreferenceValidator
{
    private const ALLOWED_PREFERENCES = [
        'theme' => ['dark', 'light'],
        'display' => ['list', 'grid'],
        'shell' => ['bash', 'lshell'],
        'lang' => ['en', 'fr'],
    ];

    public function validate(string $preferenceKey, Request $request): void
    {
        if (! array_key_exists($preferenceKey, self::ALLOWED_PREFERENCES)) {
            throw new \InvalidArgumentException(sprintf('Invalid preference key: %s', $preferenceKey));
        }

        $data = json_decode($request->getContent(), true);
        if (! isset($data[$preferenceKey])) {
            $key = array_keys($data)[0];
            throw new \InvalidArgumentException(sprintf('Wrong key used for this method. Key used: %s', $key));
        }
    }

    public function isAllowedValue(string $preferenceKey, mixed $value): bool
    {
        return in_array($value, self::ALLOWED_PREFERENCES[$preferenceKey], true);
    }
}
