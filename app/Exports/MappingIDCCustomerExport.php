<?php
namespace App\Exports;

use App\Models\MappingIDCCustomer;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class MappingIDCCustomerExport
{
    private const ROW_HEIGHT = 80;
    private const IMG_HEIGHT = 70;

    private array $headings = [
        'kode_avid',
        'id_sfa',
        'ImageTokoAvian',
        'ImageTokoAvian1',
    ];

    private array $tempFiles = [];

    public function __construct(
        private ?string $syncStatus = null
    ) {}

    public function download(string $filename = 'export_mapping_idc_customer.xlsx'): StreamedResponse
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
            $this->cleanupTempFiles();
        }, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }

    private function fillData(Worksheet $sheet): void
    {
        $query = MappingIDCCustomer::query()->select([
            'IDC',
            'id_sfa',
            'ImageTokoAvian',
            'ImageTokoAvian1',
        ]);

        if ($this->syncStatus) {
            $query->where('sync_status', $this->syncStatus);
        }

        $rowIndex = 2;

        $query->orderBy('IDC')->chunk(200, function ($records) use ($sheet, &$rowIndex) {
            foreach ($records as $record) {
                $idSfa = '';

                $sheet->setCellValue("A{$rowIndex}", $record->IDC);
                $sheet->setCellValue("B{$rowIndex}", $record->id_sfa ?? '');
                $sheet->getRowDimension($rowIndex)->setRowHeight(self::ROW_HEIGHT);

                $this->embedImage($sheet, resolveImageSrc($record->ImageTokoAvian), "C{$rowIndex}");
                $this->embedImage($sheet, resolveImageSrc($record->ImageTokoAvian1), "D{$rowIndex}");

                $rowIndex++;
            }
        });
    }

    private function embedImage(Worksheet $sheet, ?string $imageSrc, string $cell): void
    {
        if (empty($imageSrc)) {
            return;
        }

        try {
            $imageData = $this->extractImageData($imageSrc);
            if (! $imageData) {
                return;
            }

            $ext     = $this->guessExtension($imageData);
            $tmpPath = tempnam(sys_get_temp_dir(), 'idc_img_') . '.' . $ext;

            file_put_contents($tmpPath, $imageData);
            $this->tempFiles[] = $tmpPath;

            $drawing = new Drawing();
            $drawing->setPath($tmpPath);
            $drawing->setHeight(self::IMG_HEIGHT);
            $drawing->setCoordinates($cell);
            $drawing->setOffsetX(2);
            $drawing->setOffsetY(2);
            $drawing->setWorksheet($sheet);

        } catch (Throwable) {
            // gambar tidak tersedia tidak boleh gagalkan export
        }
    }

    private function extractImageData(string $imageSrc): ?string
    {
        if (str_starts_with($imageSrc, 'data:')) {
            $commaPos = strpos($imageSrc, ',');
            if ($commaPos === false) {
                return null;
            }
            $decoded = base64_decode(substr($imageSrc, $commaPos + 1), true);
            return $decoded ?: null;
        }

        $decoded = base64_decode($imageSrc, true);
        if ($decoded && $this->isImageBytes($decoded)) {
            return $decoded;
        }

        return null;
    }

    private function isImageBytes(string $data): bool
    {
        $header = substr($data, 0, 4);
        return str_starts_with($header, "\x89PNG")
        || str_starts_with($header, "\xFF\xD8\xFF")
        || str_starts_with($header, 'GIF8')
        || str_starts_with($header, 'RIFF');
    }

    private function guessExtension(string $data): string
    {
        $header = substr($data, 0, 4);

        return match (true) {
            str_starts_with($header, "\x89PNG") => 'png',
            str_starts_with($header, 'GIF8')    => 'gif',
            str_starts_with($header, 'RIFF')    => 'webp',
            default                             => 'jpg',
        };
    }

    private function applyHeaderStyle(Worksheet $sheet): void
    {
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
    }

    private function cleanupTempFiles(): void
    {
        foreach ($this->tempFiles as $path) {
            if (file_exists($path)) {
                @unlink($path);
            }
        }
    }
}
