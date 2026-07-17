<?php

declare(strict_types=1);

namespace Wachplaner\Core\System\Status;

use Wachplaner\Core\System\Health\HealthCheck;
use Wachplaner\Core\System\MaintenanceState;

final class SystemStatus
{
    public function __construct(
        private readonly HealthCheck $healthCheck,
        private readonly MaintenanceState $maintenanceState,
        private readonly Version $version
    ) {
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'version' => $this->version->toArray(),
            'maintenance' => $this->maintenanceState->toArray(),
            'health' => $this->healthCheck->report(),
            'generated_at' => date(DATE_ATOM),
        ];
    }
}
