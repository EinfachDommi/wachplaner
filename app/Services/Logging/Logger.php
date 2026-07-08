<?php

namespace Wachplaner\Services\Logging;

final class Logger
{
    public function __construct(private string $logDir)
    {
        if (!is_dir($this->logDir)) {
            @mkdir($this->logDir, 0775, true);
        }
    }

    public function info(string $message, array $context = [], string $channel = 'app'): void
    {
        $this->write('INFO', $message, $context, $channel);
    }

    public function warning(string $message, array $context = [], string $channel = 'app'): void
    {
        $this->write('WARNING', $message, $context, $channel);
    }

    public function error(string $message, array $context = [], string $channel = 'app'): void
    {
        $this->write('ERROR', $message, $context, $channel);
    }

    public function critical(string $message, array $context = [], string $channel = 'app'): void
    {
        $this->write('CRITICAL', $message, $context, $channel);
    }

    private function write(string $level, string $message, array $context = [], string $channel = 'app'): void
    {
        $channel = preg_replace('/[^a-zA-Z0-9_-]/', '', $channel) ?: 'app';
        $line = sprintf(
            "[%s] %s %s %s\n",
            date('Y-m-d H:i:s'),
            $level,
            $message,
            $context ? json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : ''
        );

        @file_put_contents($this->logDir . '/' . $channel . '.log', $line, FILE_APPEND | LOCK_EX);
    }
}
