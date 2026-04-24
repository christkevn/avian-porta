<?php
namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PendingIDCCustomerExport
{
    private array $headings = [
        'No',
        'IDC',
        'Nama',
        'Status SFA',
        'Alamat1',
        'Alamat2',
        'Kabupaten',
        'Kecamatan',
        'Telepon',
        'HP',
        'Longitude',
        'Latitude',
        'Tanggal Proses',
    ];

    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function download(string $filename): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        $sheet->setTitle('Pending IDC Customer');

        $sheet->fromArray([$this->headings], null, 'A1');
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($this->headings));

        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font'      => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 11,
            ],
            'fill'      => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'borders'   => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(25);

        $rowIndex = 2;
        $no       = 1;
        foreach ($this->data as $item) {
            $sheet->setCellValue('A' . $rowIndex, $no);
            $sheet->setCellValue('B' . $rowIndex, $item['IDC'] ?? '-');
            $sheet->setCellValue('C' . $rowIndex, $item['Nama'] ?? '-');
            $sheet->setCellValue('D' . $rowIndex, $item['sfa_status_label'] ?? '-');
            $sheet->setCellValue('E' . $rowIndex, $item['Alamat1'] ?? '-');
            $sheet->setCellValue('F' . $rowIndex, $item['Alamat2'] ?? '-');
            $sheet->setCellValue('G' . $rowIndex, $item['Kabupaten'] ?? '-');
            $sheet->setCellValue('H' . $rowIndex, $item['Kecamatan'] ?? '-');
            $sheet->setCellValue('I' . $rowIndex, $item['Telepon'] ?? '-');
            $sheet->setCellValue('J' . $rowIndex, $item['HP'] ?? '-');
            $sheet->setCellValue('K' . $rowIndex, $item['Longitude'] ?? '-');
            $sheet->setCellValue('L' . $rowIndex, $item['Latitude'] ?? '-');
            $sheet->setCellValue('M' . $rowIndex, $item['created_at'] ?? '-');

            $sheet->getStyle("A{$rowIndex}:{$lastCol}{$rowIndex}")->applyFromArray([
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders'   => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'CCCCCC'],
                    ],
                ],
            ]);

            $sheet->getRowDimension($rowIndex)->setRowHeight(20);
            $rowIndex++;
            $no++;
        }

        foreach (range(1, count($this->headings)) as $col) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }

        $sheet->freezePane('A2');
        $sheet->getSheetView()->setZoomScale(100);

        $writer = new Xlsx($spreadsheet);

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
