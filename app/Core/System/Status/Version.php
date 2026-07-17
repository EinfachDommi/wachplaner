<?php

declare(strict_types=1);

namespace Wachplaner\Core\System\Status;

final class Version
{
    public function __construct(
        public readonly string $number,
        public readonly string $codename,
        public readonly string $build,
        public readonly string $environment
    ) {
    }

    public static function fromConfig(): self
    {
        return new self(
            (string) \Config::get('version.number', 'unknown'),
            (string) \Config::get('version.codename', 'unknown'),
            (string) \Config::get('version.build', 'unknown'),
            (string) \Config::get('app_env', 'unknown')
        );
    }

    /** @return array{number:string,codename:string,build:string,environment:string} */
    public function toArray(): array
    {
        return [
            'number' => $this->number,
            'codename' => $this->codename,
            'build' => $this->build,
            'environment' => $this->environment,
        ];
    }
}
