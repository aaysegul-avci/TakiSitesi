<?php

declare(strict_types=1);

function pdfEscape(string $text): string
{
    return str_replace(
        ['\\', '(', ')'],
        ['\\\\', '\\(', '\\)'],
        $text
    );
}

function outputSimplePdf(string $filename, string $title, array $lines): void
{
    $linesPerPage = 42;
    $pages = array_chunk($lines, $linesPerPage);
    if ($pages === []) {
        $pages = [['Kayit bulunamadi.']];
    }

    $objects = [];

    $objects[] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>";
    $fontObjectId = count($objects);

    $pageObjectIds = [];
    $contentObjectIds = [];

    foreach ($pages as $pageLines) {
        $content = "BT\n/F1 12 Tf\n50 792 Td\n14 TL\n";
        $content .= '(' . pdfEscape($title) . ") Tj\nT*\n";

        foreach ($pageLines as $line) {
            $content .= '(' . pdfEscape($line) . ") Tj\nT*\n";
        }

        $content .= "ET";

        $stream = "<< /Length " . strlen($content) . " >>\nstream\n" . $content . "\nendstream";
        $objects[] = $stream;
        $contentObjectIds[] = count($objects);

        $objects[] = '';
        $pageObjectIds[] = count($objects);
    }

    $kids = [];
    foreach ($pageObjectIds as $pageObjectId) {
        $kids[] = $pageObjectId . " 0 R";
    }

    $objects[] = "<< /Type /Pages /Count " . count($pageObjectIds) . " /Kids [" . implode(' ', $kids) . "] >>";
    $pagesObjectId = count($objects);

    foreach ($pageObjectIds as $index => $pageObjectId) {
        $contentObjectId = $contentObjectIds[$index];
        $objects[$pageObjectId - 1] = "<< /Type /Page /Parent {$pagesObjectId} 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 {$fontObjectId} 0 R >> >> /Contents {$contentObjectId} 0 R >>";
    }

    $objects[] = "<< /Type /Catalog /Pages {$pagesObjectId} 0 R >>";
    $catalogObjectId = count($objects);

    $pdf = "%PDF-1.4\n";
    $offsets = [0];

    foreach ($objects as $index => $object) {
        $offsets[] = strlen($pdf);
        $pdf .= ($index + 1) . " 0 obj\n" . $object . "\nendobj\n";
    }

    $xrefOffset = strlen($pdf);
    $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
    $pdf .= "0000000000 65535 f \n";

    for ($i = 1; $i <= count($objects); $i++) {
        $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
    }

    $pdf .= "trailer << /Size " . (count($objects) + 1) . " /Root {$catalogObjectId} 0 R >>\n";
    $pdf .= "startxref\n{$xrefOffset}\n%%EOF";

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename=' . $filename);
    echo $pdf;
    exit;
}
