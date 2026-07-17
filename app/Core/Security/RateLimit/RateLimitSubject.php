<?php

declare(strict_types=1);

namespace Wachplaner\Core\Security\RateLimit;

final class RateLimitSubject
{
    /**
     * @param list<string|null> $parts
     */
    public static function hash(string $scope, array $parts): string
    {
        if (!RateLimitScope::isValid($scope)) {
            throw new \InvalidArgumentException('Unbekannter Rate-Limit-Scope.');
        }

        $normalized = array_map(
            static fn (?string $part): string => self::normalize((string) $part),
            $parts
        );

        return hash('sha256', $scope . '|' . implode('|', $normalized));
    }

    private static function normalize(string $value): string
    {
        $value = trim(mb_strtolower($value, 'UTF-8'));
        $value = preg_replace('/\s+/u', ' ', $value) ?? $value;

        return mb_substr($value, 0, 500, 'UTF-8');
    }
}
