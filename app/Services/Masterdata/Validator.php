<?php

declare(strict_types=1);

namespace Wachplaner\Services\Masterdata;

final class Validator
{
    private const ALLOWED_EXTENSIONS = ['xlsx'];

    public function __construct(
        private readonly int $maxFileSizeBytes = 15728640
    ) {
    }

    public function validateImportFile(string $filePath, ?string $originalFileName = null): array
    {
        $errors = [];

        if ($filePath === '') {
            return ['Es wurde kein Dateipfad übergeben.'];
        }

        if (!is_file($filePath)) {
            return ['Die Importdatei wurde nicht gefunden.'];
        }

        if (!is_readable($filePath)) {
            $errors[] = 'Die Importdatei ist nicht lesbar.';
        }

        $fileSize = filesize($filePath);
        if ($fileSize === false) {
            $errors[] = 'Die Größe der Importdatei konnte nicht ermittelt werden.';
        } elseif ($fileSize === 0) {
            $errors[] = 'Die Importdatei ist leer.';
        } elseif ($fileSize > $this->maxFileSizeBytes) {
            $errors[] = sprintf(
                'Die Importdatei überschreitet die maximal erlaubte Größe von %.1f MB.',
                $this->maxFileSizeBytes / 1024 / 1024
            );
        }

        $name = $originalFileName ?: basename($filePath);
        $extension = strtolower((string) pathinfo($name, PATHINFO_EXTENSION));
        if ($extension === '' || !in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            $errors[] = 'Es werden ausschließlich XLSX-Dateien unterstützt.';
        }

        return $errors;
    }
}
