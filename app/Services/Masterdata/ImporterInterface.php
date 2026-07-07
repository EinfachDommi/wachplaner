<?php

namespace Wachplaner\Services\Masterdata;

interface ImporterInterface
{
    public function key(): string;

    public function label(): string;

    public function expectedColumns(): array;

    public function import(string $filePath): ImportResult;
}
