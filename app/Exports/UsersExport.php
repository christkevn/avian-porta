<?php
namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UsersExport
{
    private array $headings = [
        'No',
        'Username',
        'Tipe',
        'Nama',
        'Email',
        'Level',
        'Cabang',
        'Status',
    ];

    private iterable $data;

    public function __construct(iterable $data)
    {
        $this->data = $data;
    }

    public function download(string $filename): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Users');

        $sheet->fromArray([$this->headings], null, 'A1');
        $lastCol = Coordinate::stringFromColumnIndex(count($this->headings));

        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(25);

        $rowIndex = 2;
        $no       = 1;
        foreach ($this->data as $item) {
            $sheet->setCellValue('A' . $rowIndex, $no);
            $sheet->setCellValueExplicit('B' . $rowIndex, (string) ($item->username ?? '-'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('C' . $rowIndex, $item->tipe == 'AD' ? 'ADMIN' : 'USER');
            $sheet->setCellValue('D' . $rowIndex, $item->nama ?? '-');
            $sheet->setCellValue('E' . $rowIndex, $item->email ?? '-');
            $sheet->setCellValue('F' . $rowIndex, $item->level ?? '-');
            $sheet->setCellValue('G' . $rowIndex, $item->cabang ?? '-');
            $sheet->setCellValue('H' . $rowIndex, $item->aktif == 1 ? 'Aktif' : 'Nonaktif');

            $sheet->getStyle("A{$rowIndex}:{$lastCol}{$rowIndex}")->applyFromArray([
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
            ]);
            $rowIndex++;
            $no++;
        }

        foreach (range(1, count($this->headings)) as $col) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }
        $sheet->freezePane('A2');

        $writer = new Xlsx($spreadsheet);

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control'       => 'max-age=0',
        ]);
    }
}
