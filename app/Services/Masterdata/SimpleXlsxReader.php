<?php

namespace Wachplaner\Services\Masterdata;

use RuntimeException;
use ZipArchive;

final class SimpleXlsxReader
{
    public function readRows(string $filePath): array
    {
        if (!class_exists(ZipArchive::class)) {
            throw new RuntimeException('PHP-Erweiterung zip ist erforderlich, um XLSX-Dateien zu lesen.');
        }

        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            throw new RuntimeException('XLSX-Datei konnte nicht geöffnet werden.');
        }

        $sharedStrings = $this->readSharedStrings($zip);
        $sheetPath = $this->firstWorksheetPath($zip);
        $xml = $zip->getFromName($sheetPath);
        $zip->close();

        if ($xml === false) {
            throw new RuntimeException('Erstes Tabellenblatt konnte nicht gelesen werden.');
        }

        $document = simplexml_load_string($xml);
        $document->registerXPathNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $rows = [];

        foreach ($document->xpath('//x:sheetData/x:row') as $row) {
            $values = [];
            foreach ($row->c as $cell) {
                $ref = (string)$cell['r'];
                $index = $this->columnIndex($ref);
                $value = $this->cellValue($cell, $sharedStrings);
                $values[$index] = trim((string)$value);
            }
            if ($values !== []) {
                ksort($values);
                $max = max(array_keys($values));
                $normalized = [];
                for ($i = 1; $i <= $max; $i++) {
                    $normalized[] = $values[$i] ?? '';
                }
                $rows[] = $normalized;
            }
        }

        return $rows;
    }

    public function readAssoc(string $filePath, int $headerRowIndex = 0): array
    {
        $rows = $this->readRows($filePath);
        if (!isset($rows[$headerRowIndex])) {
            return [];
        }

        $headers = array_map([$this, 'normalizeHeader'], $rows[$headerRowIndex]);
        $assoc = [];

        for ($i = $headerRowIndex + 1; $i < count($rows); $i++) {
            $item = [];
            $empty = true;
            foreach ($headers as $idx => $header) {
                if ($header === '') {
                    continue;
                }
                $value = $rows[$i][$idx] ?? '';
                if ($value !== '') {
                    $empty = false;
                }
                $item[$header] = $value;
            }
            if (!$empty) {
                $assoc[] = $item;
            }
        }

        return $assoc;
    }

    private function readSharedStrings(ZipArchive $zip): array
    {
        $xml = $zip->getFromName('xl/sharedStrings.xml');
        if ($xml === false) {
            return [];
        }

        $document = simplexml_load_string($xml);
        $document->registerXPathNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $strings = [];
        foreach ($document->xpath('//x:si') as $si) {
            $text = '';
            foreach ($si->xpath('.//x:t') as $t) {
                $text .= (string)$t;
            }
            $strings[] = $text;
        }

        return $strings;
    }

    private function firstWorksheetPath(ZipArchive $zip): string
    {
        if ($zip->locateName('xl/worksheets/sheet1.xml') !== false) {
            return 'xl/worksheets/sheet1.xml';
        }

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (str_starts_with($name, 'xl/worksheets/sheet') && str_ends_with($name, '.xml')) {
                return $name;
            }
        }

        throw new RuntimeException('Keine Arbeitsmappe in XLSX-Datei gefunden.');
    }

    private function cellValue($cell, array $sharedStrings): string
    {
        $type = (string)$cell['t'];
        $raw = isset($cell->v) ? (string)$cell->v : '';

        if ($type === 's') {
            return $sharedStrings[(int)$raw] ?? '';
        }

        if ($type === 'inlineStr' && isset($cell->is->t)) {
            return (string)$cell->is->t;
        }

        return $raw;
    }

    private function columnIndex(string $cellReference): int
    {
        preg_match('/^[A-Z]+/i', $cellReference, $matches);
        $letters = strtoupper($matches[0] ?? 'A');
        $number = 0;
        for ($i = 0; $i < strlen($letters); $i++) {
            $number = $number * 26 + (ord($letters[$i]) - 64);
        }
        return $number;
    }

    private function normalizeHeader(string $header): string
    {
        $header = str_replace(["\r", "\n"], ' ', trim($header));
        $header = preg_replace('/\s+/', ' ', $header);
        return mb_strtolower($header, 'UTF-8');
    }
}
