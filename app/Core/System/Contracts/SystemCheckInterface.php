<?php

declare(strict_types=1);

namespace Wachplaner\Core\System\Contracts;

use Wachplaner\Core\System\Health\CheckResult;

interface SystemCheckInterface
{
    public function key(): string;

    public function run(): CheckResult;
}
