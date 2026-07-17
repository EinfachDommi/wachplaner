<?php

declare(strict_types=1);

namespace Wachplaner\Services\Masterdata;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;
use RuntimeException;
use ZipArchive;

final class SimpleXlsxReader
{
    private const SPREADSHEET_NAMESPACE =
        'http://schemas.openxmlformats.org/spreadsheetml/2006/main';

    /**
     * @return list<list<string>>
     */
    public function readRows(string $filePath): array
    {
        if (!is_file($filePath) || !is_readable($filePath)) {
            throw new RuntimeException('XLSX-Datei wurde nicht gefunden oder ist nicht lesbar.');
        }

        $zip = new ZipArchive();

        if ($zip->open($filePath) !== true) {
            throw new RuntimeException('XLSX-Datei konnte nicht geöffnet werden.');
        }

        try {
            $sharedStrings = $this->readSharedStrings($zip);
            $worksheetPath = $this->firstWorksheetPath($zip);
            $worksheetXml = $zip->getFromName($worksheetPath);

            if ($worksheetXml === false) {
                throw new RuntimeException('Erstes Tabellenblatt konnte nicht gelesen werden.');
            }

            return $this->parseWorksheet($worksheetXml, $sharedStrings);
        } finally {
            $zip->close();
        }
    }

    /**
     * @return list<array<string, string>>
     */
    public function readAssoc(string $filePath, int $headerRowIndex = 0): array
    {
        $rows = $this->readRows($filePath);

        if (!isset($rows[$headerRowIndex])) {
            return [];
        }

        $headers = array_map(
            fn (string $header): string => $this->normalizeHeader($header),
            $rows[$headerRowIndex]
        );

        $result = [];

        for ($rowIndex = $headerRowIndex + 1, $rowCount = count($rows);
            $rowIndex < $rowCount;
            $rowIndex++
        ) {
            $item = [];
            $containsValue = false;

            foreach ($headers as $columnIndex => $header) {
                if ($header === '') {
                    continue;
                }

                $value = trim((string) ($rows[$rowIndex][$columnIndex] ?? ''));

                if ($value !== '') {
                    $containsValue = true;
                }

                $item[$header] = $value;
            }

            if ($containsValue) {
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * @param list<string> $sharedStrings
     * @return list<list<string>>
     */
    private function parseWorksheet(string $xml, array $sharedStrings): array
    {
        [$document, $xpath] = $this->loadSpreadsheetXml(
            $xml,
            'Das Tabellenblatt enthält ungültiges XML.'
        );

        $rowNodes = $xpath->query('//x:sheetData/x:row');

        if ($rowNodes === false) {
            throw new RuntimeException('Die Zeilen des Tabellenblatts konnten nicht gelesen werden.');
        }

        $rows = [];

        foreach ($rowNodes as $rowNode) {
            if (!$rowNode instanceof DOMElement) {
                continue;
            }

            $cellNodes = $xpath->query('./x:c', $rowNode);

            if ($cellNodes === false) {
                continue;
            }

            $values = [];

            foreach ($cellNodes as $cellNode) {
                if (!$cellNode instanceof DOMElement) {
                    continue;
                }

                $reference = $cellNode->getAttribute('r');
                $columnIndex = $this->columnIndex($reference);
                $values[$columnIndex] = trim(
                    $this->cellValue($cellNode, $xpath, $sharedStrings)
                );
            }

            if ($values === []) {
                continue;
            }

            ksort($values);

            $highestColumn = max(array_keys($values));
            $normalizedRow = [];

            for ($column = 1; $column <= $highestColumn; $column++) {
                $normalizedRow[] = $values[$column] ?? '';
            }

            $rows[] = $normalizedRow;
        }

        return $rows;
    }

    /**
     * @return list<string>
     */
    private function readSharedStrings(ZipArchive $zip): array
    {
        $xml = $zip->getFromName('xl/sharedStrings.xml');

        if ($xml === false) {
            return [];
        }

        [, $xpath] = $this->loadSpreadsheetXml(
            $xml,
            'Die gemeinsam genutzten Zeichenketten enthalten ungültiges XML.'
        );

        $stringNodes = $xpath->query('//x:si');

        if ($stringNodes === false) {
            throw new RuntimeException('Die Textwerte der XLSX-Datei konnten nicht gelesen werden.');
        }

        $strings = [];

        foreach ($stringNodes as $stringNode) {
            if (!$stringNode instanceof DOMElement) {
                continue;
            }

            $textNodes = $xpath->query('.//x:t', $stringNode);

            if ($textNodes === false) {
                $strings[] = '';
                continue;
            }

            $value = '';

            foreach ($textNodes as $textNode) {
                $value .= $textNode->textContent;
            }

            $strings[] = $value;
        }

        return $strings;
    }

    private function firstWorksheetPath(ZipArchive $zip): string
    {
        if ($zip->locateName('xl/worksheets/sheet1.xml') !== false) {
            return 'xl/worksheets/sheet1.xml';
        }

        for ($index = 0; $index < $zip->numFiles; $index++) {
            $name = $zip->getNameIndex($index);

            if (
                is_string($name)
                && str_starts_with($name, 'xl/worksheets/sheet')
                && str_ends_with($name, '.xml')
            ) {
                return $name;
            }
        }

        throw new RuntimeException('In der XLSX-Datei wurde kein Tabellenblatt gefunden.');
    }

    /**
     * @param list<string> $sharedStrings
     */
    private function cellValue(
        DOMElement $cell,
        DOMXPath $xpath,
        array $sharedStrings
    ): string {
        $type = $cell->getAttribute('t');

        if ($type === 'inlineStr') {
            $textNodes = $xpath->query('./x:is//x:t', $cell);

            if ($textNodes === false) {
                return '';
            }

            $value = '';

            foreach ($textNodes as $textNode) {
                $value .= $textNode->textContent;
            }

            return $value;
        }

        $valueNodes = $xpath->query('./x:v', $cell);
        $valueNode = $valueNodes !== false ? $valueNodes->item(0) : null;
        $rawValue = $valueNode instanceof DOMNode ? $valueNode->textContent : '';

        return match ($type) {
            's' => $sharedStrings[(int) $rawValue] ?? '',
            'b' => $rawValue === '1' ? '1' : '0',
            'str', 'e' => $rawValue,
            default => $rawValue,
        };
    }

    private function columnIndex(string $cellReference): int
    {
        if (!preg_match('/^[A-Z]+/i', $cellReference, $matches)) {
            return 1;
        }

        $letters = strtoupper($matches[0]);
        $column = 0;

        foreach (str_split($letters) as $letter) {
            $column = ($column * 26) + (ord($letter) - 64);
        }

        return max(1, $column);
    }

    private function normalizeHeader(string $header): string
    {
        $header = str_replace(["\r", "\n"], ' ', trim($header));
        $header = preg_replace('/\s+/u', ' ', $header) ?? $header;

        return mb_strtolower($header, 'UTF-8');
    }

    /**
     * @return array{DOMDocument, DOMXPath}
     */
    private function loadSpreadsheetXml(string $xml, string $errorMessage): array
    {
        $previousSetting = libxml_use_internal_errors(true);
        libxml_clear_errors();

        try {
            $document = new DOMDocument();

            if (
                !$document->loadXML(
                    $xml,
                    LIBXML_NONET | LIBXML_COMPACT | LIBXML_NOBLANKS
                )
            ) {
                throw new RuntimeException($errorMessage);
            }

            $xpath = new DOMXPath($document);
            $xpath->registerNamespace('x', self::SPREADSHEET_NAMESPACE);

            return [$document, $xpath];
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previousSetting);
        }
    }
}
