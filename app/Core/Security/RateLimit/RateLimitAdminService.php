<?php

declare(strict_types=1);

namespace Wachplaner\Core\Security\RateLimit;

final class RateLimitAdminService
{
    public function __construct(
        private readonly RateLimitRepository $repository
    ) {
    }

    /**
     * @return array{
     *   total:int,
     *   by_scope:array<string,int>,
     *   entries:list<array<string,mixed>>
     * }
     */
    public function activeBlocks(int $limit = 100): array
    {
        $entries = $this->repository->activeBlocks($limit);
        $byScope = [];

        foreach ($entries as $entry) {
            $scope = (string) ($entry['scope'] ?? 'unknown');
            $byScope[$scope] = ($byScope[$scope] ?? 0) + 1;
        }

        return [
            'total' => count($entries),
            'by_scope' => $byScope,
            'entries' => $entries,
        ];
    }

    public function clear(string $scope, string $subjectHash): void
    {
        $this->repository->clear($scope, $subjectHash);
    }
}
