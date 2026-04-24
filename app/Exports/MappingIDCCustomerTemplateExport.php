<?php
namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MappingIDCCustomerTemplateExport
{
    private array $headings = [
        'kode_avid',
        'id_sfa',
        'ImageTokoAvian',
        'ImageTokoAvian1',
    ];

    private array $sampleRows = [
        ['IDC-001', 'SFA-001'],
        ['IDC-002', ''],
    ];

    public function download(string $filename): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        $sheet->fromArray([$this->headings], null, 'A1');
        $sheet->fromArray($this->sampleRows, null, 'A2');

        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($this->headings));

        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font'      => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill'      => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        foreach (range(1, count($this->headings)) as $col) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }

        $this->addImage($sheet, public_path('sample.jpg'), 'C3');
        $this->addImage($sheet, public_path('sample1.jpg'), 'D3');

        $sheet->getRowDimension(2)->setRowHeight(80);
        $sheet->getRowDimension(3)->setRowHeight(80);

        $writer = new Xlsx($spreadsheet);

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }

    private function addImage($sheet, string $path, string $cell): void
    {
        if (! file_exists($path)) {
            return;
        }

        $drawing = new Drawing();
        $drawing->setPath($path);
        $drawing->setHeight(70);
        $drawing->setCoordinates($cell);
        $drawing->setWorksheet($sheet);
    }
}
