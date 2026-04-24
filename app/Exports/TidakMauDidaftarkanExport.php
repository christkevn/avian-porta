<?php
namespace App\Exports;

use App\Models\MappingIDCCustomer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TidakMauDidaftarkanExport
{
    private array $headings = [
        'IDC', 'Nama', 'Alamat1', 'Alamat2', 'Kabupaten', 'Kecamatan',
        'Telepon', 'HP', 'Longitude', 'Latitude', 'Tanggal Proses',
        'Kode Tirta (BR)', 'Kode Tirta (AVID)',
        'Membership', 'KTP', 'NPWP', 'Nama PKP', 'Alamat PKP',
    ];

    public function __construct(
        private ?string $nama = null,
        private ?string $tanggalStart = null,
        private ?string $tanggalEnd = null,
    ) {}

    public function download(string $filename): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        $sheet->fromArray([$this->headings], null, 'A1');
        $this->applyHeaderStyle($sheet);
        $this->fillData($sheet);

        foreach (range(1, count($this->headings)) as $col) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
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

    private function buildData(): array
    {
        $idcList = DB::connection('SFA-TIRTA')
            ->table('BR_CustomerIDC')
            ->whereNotNull('ApprovedBy')
            ->whereNotIn('Status', [1, 2, 0])
            ->orderBy('IDC')
            ->pluck('IDC')
            ->toArray();

        $existingIDCs = collect($idcList)
            ->chunk(1000)
            ->flatMap(fn($chunk) => MappingIDCCustomer::whereIn('IDC', $chunk->values())->pluck('IDC'))
            ->unique()
            ->values()
            ->toArray();

        $avidCustomerMap = DB::connection('AVIDPusat')
            ->table('Customer')
            ->whereIn('Customer', $existingIDCs)
            ->get(['Customer', 'CustTirta', 'Membership', 'KTP', 'NPWP', 'NamaPKP', 'AlamatPKP'])
            ->keyBy('Customer')
            ->toArray();

        $idcTirtaMap = DB::connection('SFA-TIRTA')
            ->table('BR_CustomerIDC')
            ->whereIn('IDC', $existingIDCs)
            ->pluck('Tirta', 'IDC')
            ->toArray();

        $avidCodeMap   = [];
        $avidDetailMap = [];

        foreach ($existingIDCs as $idc) {
            $avid      = $avidCustomerMap[$idc] ?? null;
            $custTirta = $avid->CustTirta ?? null;
            $mapped    = ! is_null($custTirta) && $custTirta !== '';

            $avidCodeMap[$idc]   = $mapped ? $custTirta : null;
            $avidDetailMap[$idc] = $avid;
        }

        return [
            'existingIDCs'  => $existingIDCs,
            'idcTirtaMap'   => $idcTirtaMap,
            'avidCodeMap'   => $avidCodeMap,
            'avidDetailMap' => $avidDetailMap,
        ];
    }

    private function fillData(Worksheet $sheet): void
    {
        [
            'existingIDCs'  => $idcList,
            'idcTirtaMap'   => $idcTirtaMap,
            'avidCodeMap'   => $avidCodeMap,
            'avidDetailMap' => $avidDetailMap,
        ] = $this->buildData();

        $query = MappingIDCCustomer::whereIn('IDC', $idcList)
            ->select([
                'IDC', 'Nama', 'Alamat1', 'Alamat2', 'Kabupaten', 'Kecamatan',
                'Telepon', 'HP', 'Longitude', 'Latitude', 'created_at',
            ]);

        if ($this->nama) {
            $query->where('Nama', 'like', '%' . $this->nama . '%');
        }
        if ($this->tanggalStart) {
            $query->whereDate('created_at', '>=', Carbon::parse($this->tanggalStart)->startOfDay());
        }
        if ($this->tanggalEnd) {
            $query->whereDate('created_at', '<=', Carbon::parse($this->tanggalEnd)->endOfDay());
        }

        $rowIndex = 2;

        $query->orderBy('IDC')->chunk(500, function ($records) use ($sheet, &$rowIndex, $idcTirtaMap, $avidCodeMap, $avidDetailMap) {
            foreach ($records as $r) {
                $avid = $avidDetailMap[$r->IDC] ?? null;

                $sheet->fromArray([[
                    $r->IDC,
                    $r->Nama,
                    $r->Alamat1,
                    $r->Alamat2,
                    $r->Kabupaten,
                    $r->Kecamatan,
                    $r->Telepon ? implode('; ', array_filter(array_map('trim', explode(';', $r->Telepon)))) : '',
                    $r->HP ? implode('; ', array_filter(array_map('trim', explode(';', $r->HP)))) : '',
                    $r->Longitude,
                    $r->Latitude,
                    $r->created_at ? Carbon::parse($r->created_at)->format('d-m-Y') : '',
                    $idcTirtaMap[$r->IDC] ?? '-',
                    $avidCodeMap[$r->IDC] ?? '-',
                    $avid->Membership ?? '-',
                    $avid->KTP ?? '-',
                    $avid->NPWP ?? '-',
                    $avid->NamaPKP ?? '-',
                    $avid->AlamatPKP ?? '-',
                ]], null, "A{$rowIndex}");

                $rowIndex++;
            }
        });
    }

    private function applyHeaderStyle(Worksheet $sheet): void
    {
        $lastCol = Coordinate::stringFromColumnIndex(count($this->headings));

        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
    }
}
