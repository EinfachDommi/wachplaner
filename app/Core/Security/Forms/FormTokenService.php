<?php

declare(strict_types=1);

namespace Wachplaner\Core\Security\Forms;

use RuntimeException;

final class FormTokenService
{
    private const SESSION_KEY = '_shield_form_tokens';

    public function __construct(
        private readonly string $secret,
        private readonly int $minimumAgeSeconds = 2,
        private readonly int $maximumAgeSeconds = 7200,
        private readonly int $maximumOutstandingTokens = 50
    ) {
        if (strlen($secret) < 32) {
            throw new RuntimeException(
                'Der Form-Token-Schlüssel muss mindestens 32 Zeichen lang sein.'
            );
        }
    }

    public function issue(string $formId): FormToken
    {
        $this->requireSession();

        $formId = $this->normalizeFormId($formId);
        $issuedAt = time();
        $nonce = bin2hex(random_bytes(16));
        $payload = $this->encodePayload([
            'form' => $formId,
            'iat' => $issuedAt,
            'nonce' => $nonce,
        ]);

        $signature = hash_hmac('sha256', $payload, $this->secret);
        $token = $payload . '.' . $signature;

        $this->storeIssuedToken($formId, $nonce, $issuedAt);

        return new FormToken($token, $nonce, $issuedAt);
    }

    public function validateAndConsume(
        string $formId,
        string $token
    ): FormProtectionResult {
        $this->requireSession();

        $formId = $this->normalizeFormId($formId);
        $parts = explode('.', $token, 2);

        if (count($parts) !== 2) {
            return FormProtectionResult::invalid('form_token_malformed');
        }

        [$payload, $signature] = $parts;
        $expectedSignature = hash_hmac('sha256', $payload, $this->secret);

        if (!hash_equals($expectedSignature, $signature)) {
            return FormProtectionResult::invalid('form_token_signature');
        }

        $data = $this->decodePayload($payload);

        if (
            !is_array($data)
            || ($data['form'] ?? null) !== $formId
            || !is_int($data['iat'] ?? null)
            || !is_string($data['nonce'] ?? null)
        ) {
            return FormProtectionResult::invalid('form_token_payload');
        }

        $issuedAt = $data['iat'];
        $nonce = $data['nonce'];
        $age = time() - $issuedAt;

        if ($age < $this->minimumAgeSeconds) {
            return FormProtectionResult::invalid('form_submitted_too_fast');
        }

        if ($age > $this->maximumAgeSeconds) {
            $this->forgetToken($formId, $nonce);

            return FormProtectionResult::invalid('form_token_expired');
        }

        if (!$this->isOutstanding($formId, $nonce, $issuedAt)) {
            return FormProtectionResult::invalid('form_token_replayed');
        }

        $this->forgetToken($formId, $nonce);

        return FormProtectionResult::valid();
    }

    public function cleanup(): void
    {
        $this->requireSession();

        $tokens = $_SESSION[self::SESSION_KEY] ?? [];

        if (!is_array($tokens)) {
            $_SESSION[self::SESSION_KEY] = [];
            return;
        }

        $minimumIssuedAt = time() - $this->maximumAgeSeconds;

        foreach ($tokens as $formId => $entries) {
            if (!is_array($entries)) {
                unset($tokens[$formId]);
                continue;
            }

            foreach ($entries as $nonce => $issuedAt) {
                if (!is_int($issuedAt) || $issuedAt < $minimumIssuedAt) {
                    unset($tokens[$formId][$nonce]);
                }
            }

            if (($tokens[$formId] ?? []) === []) {
                unset($tokens[$formId]);
            }
        }

        $_SESSION[self::SESSION_KEY] = $tokens;
    }

    private function storeIssuedToken(
        string $formId,
        string $nonce,
        int $issuedAt
    ): void {
        $this->cleanup();

        $tokens = $_SESSION[self::SESSION_KEY] ?? [];

        if (!isset($tokens[$formId]) || !is_array($tokens[$formId])) {
            $tokens[$formId] = [];
        }

        $tokens[$formId][$nonce] = $issuedAt;

        if (count($tokens[$formId]) > $this->maximumOutstandingTokens) {
            asort($tokens[$formId]);
            $tokens[$formId] = array_slice(
                $tokens[$formId],
                -$this->maximumOutstandingTokens,
                null,
                true
            );
        }

        $_SESSION[self::SESSION_KEY] = $tokens;
    }

    private function isOutstanding(
        string $formId,
        string $nonce,
        int $issuedAt
    ): bool {
        $stored = $_SESSION[self::SESSION_KEY][$formId][$nonce] ?? null;

        return is_int($stored) && $stored === $issuedAt;
    }

    private function forgetToken(string $formId, string $nonce): void
    {
        unset($_SESSION[self::SESSION_KEY][$formId][$nonce]);

        if (($_SESSION[self::SESSION_KEY][$formId] ?? []) === []) {
            unset($_SESSION[self::SESSION_KEY][$formId]);
        }
    }

    private function normalizeFormId(string $formId): string
    {
        $formId = trim(mb_strtolower($formId, 'UTF-8'));

        if (
            $formId === ''
            || preg_match('/^[a-z0-9._-]{1,100}$/', $formId) !== 1
        ) {
            throw new \InvalidArgumentException('Ungültige Formular-ID.');
        }

        return $formId;
    }

    /**
     * @param array<string, int|string> $payload
     */
    private function encodePayload(array $payload): string
    {
        $json = json_encode(
            $payload,
            JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES
        );

        return rtrim(
            strtr(base64_encode($json), '+/', '-_'),
            '='
        );
    }

    /**
     * @return array<string, mixed>|null
     */
    private function decodePayload(string $payload): ?array
    {
        $padding = strlen($payload) % 4;

        if ($padding > 0) {
            $payload .= str_repeat('=', 4 - $padding);
        }

        $decoded = base64_decode(
            strtr($payload, '-_', '+/'),
            true
        );

        if ($decoded === false) {
            return null;
        }

        $data = json_decode($decoded, true);

        return is_array($data) ? $data : null;
    }

    private function requireSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new RuntimeException(
                'FormTokenService benötigt eine aktive PHP-Session.'
            );
        }
    }
}
