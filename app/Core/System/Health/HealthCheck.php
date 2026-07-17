<?php

declare(strict_types=1);

namespace Wachplaner\Core\System\Health;

use Wachplaner\Core\System\Contracts\SystemCheckInterface;

final class HealthCheck
{
    /** @var list<SystemCheckInterface> */
    private array $checks;

    /** @param iterable<SystemCheckInterface> $checks */
    public function __construct(iterable $checks)
    {
        $this->checks = [];

        foreach ($checks as $check) {
            $this->checks[] = $check;
        }
    }

    /** @return list<CheckResult> */
    public function run(): array
    {
        return array_map(
            static fn (SystemCheckInterface $check): CheckResult => $check->run(),
            $this->checks
        );
    }

    /** @return array{status:string,critical_failures:int,warnings:int,checks:list<array<string,mixed>>,checked_at:string} */
    public function report(): array
    {
        $results = $this->run();
        $criticalFailures = count(array_filter(
            $results,
            static fn (CheckResult $result): bool => $result->isCriticalFailure()
        ));
        $warnings = count(array_filter(
            $results,
            static fn (CheckResult $result): bool => $result->level === 'warning'
        ));

        return [
            'status' => $criticalFailures > 0 ? 'error' : ($warnings > 0 ? 'warning' : 'ok'),
            'critical_failures' => $criticalFailures,
            'warnings' => $warnings,
            'checks' => array_map(
                static fn (CheckResult $result): array => $result->toArray(),
                $results
            ),
            'checked_at' => date(DATE_ATOM),
        ];
    }
}
