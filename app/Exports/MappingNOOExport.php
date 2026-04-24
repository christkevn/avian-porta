<?php
namespace App\Exports;

use App\Models\MappingIDCCustomer;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MappingNOOExport
{
    private const HEADINGS = [
        'No', 'IDC', 'Nama', 'Status AVID', 'Kode CustTirta',
        'Alamat1', 'Alamat2', 'Kabupaten', 'Kecamatan',
        'Telepon', 'HP', 'Longitude', 'Latitude', 'Tanggal Proses',
    ];

    public function __construct(
        private array $idcList,
        private array $statusMap,
        private array $avidCodeMap,
    ) {}

    public function download(string $filename = ''): StreamedResponse
    {
        $filename = $filename ?: 'mapping_noo_' . now()->format('Ymd_His') . '.xlsx';

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        $sheet->fromArray([self::HEADINGS], null, 'A1');
        $this->applyHeaderStyle($sheet);
        $this->fillData($sheet);

        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count(self::HEADINGS));
        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }

    private function fillData(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet): void
    {
        $rowIndex = 2;

        MappingIDCCustomer::whereIn('IDC', $this->idcList)
            ->orderBy('IDC')
            ->chunk(500, function (Collection $records) use ($sheet, &$rowIndex) {
                foreach ($records as $r) {
                    $sheet->fromArray([[
                        $rowIndex - 1,
                        $r->IDC,
                        $r->Nama,
                        $this->statusMap[$r->IDC] ?? 'Belum Mapping',
                        $this->avidCodeMap[$r->IDC] ?? '-',
                        $r->Alamat1,
                        $r->Alamat2,
                        $r->Kabupaten,
                        $r->Kecamatan,
                        $r->Telepon,
                        $r->HP,
                        $r->Longitude,
                        $r->Latitude,
                        Carbon::parse($r->created_at)->format('d-m-Y'),
                    ]], null, "A{$rowIndex}");

                    $rowIndex++;
                }
            });
    }

    private function applyHeaderStyle(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet): void
    {
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count(self::HEADINGS));

        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
    }
}
