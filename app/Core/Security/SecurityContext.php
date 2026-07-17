<?php

declare(strict_types=1);

namespace Wachplaner\Core\Security;

use Wachplaner\Core\Security\Request\RequestId;

final class SecurityContext
{
    public function __construct(
        public readonly string $requestId,
        public readonly ?int $userId,
        public readonly ?string $route,
        public readonly ?string $method,
        public readonly ?string $ipPrefixHash,
        public readonly ?string $userAgentHash
    ) {
    }

    public static function fromGlobals(?int $userId = null): self
    {
        $route = isset($_SERVER['REQUEST_URI'])
            ? parse_url((string) $_SERVER['REQUEST_URI'], PHP_URL_PATH)
            : null;

        $method = isset($_SERVER['REQUEST_METHOD'])
            ? strtoupper((string) $_SERVER['REQUEST_METHOD'])
            : null;

        $ip = self::clientIp();
        $userAgent = isset($_SERVER['HTTP_USER_AGENT'])
            ? (string) $_SERVER['HTTP_USER_AGENT']
            : null;

        return new self(
            RequestId::current(),
            $userId,
            is_string($route) ? $route : null,
            $method,
            $ip !== null ? hash('sha256', self::ipPrefix($ip)) : null,
            $userAgent !== null && $userAgent !== ''
                ? hash('sha256', mb_substr($userAgent, 0, 500, 'UTF-8'))
                : null
        );
    }

    private static function clientIp(): ?string
    {
        $candidate = $_SERVER['REMOTE_ADDR'] ?? null;

        if (!is_string($candidate) || filter_var($candidate, FILTER_VALIDATE_IP) === false) {
            return null;
        }

        return $candidate;
    }

    private static function ipPrefix(string $ip): string
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $parts = explode('.', $ip);

            return implode('.', array_slice($parts, 0, 3)) . '.0/24';
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $packed = inet_pton($ip);

            if ($packed !== false) {
                return bin2hex(substr($packed, 0, 7)) . '00/56';
            }
        }

        return 'unknown';
    }
}
