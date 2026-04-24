<?php
namespace App\Http\Controllers;

use App\Exports\MappingIDCCustomerExport;
use App\Exports\MappingIDCCustomerTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\MappingIDCCustomerImport;
use App\Models\MappingIDCCustomer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class DataNOOController extends Controller
{
    public function index()
    {
        return view('data_noo.index');
    }

    public function create()
    {
        return redirect('/admin/data-noo');
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        $record = MappingIDCCustomer::create([
             ...$validated,
            'created_by' => Session::get('userinfo')['username'] ?? 'system',
            'updated_by' => Session::get('userinfo')['username'] ?? 'system',
        ]);

        $this->syncToExternalApi($record->toArray());

        return redirect('/admin/data-noo')
            ->with('message', 'Data berhasil disimpan')
            ->with('mode', 'success');
    }

    public function edit($id)
    {
        return redirect('/admin/data-noo');
    }

    public function update(Request $request, $id)
    {
        $data      = MappingIDCCustomer::findOrFail($id);
        $validated = $this->validateData($request, $id);

        $data->fill([
             ...$validated,
            'updated_by' => Session::get('userinfo')['username'] ?? 'system',
        ])->save();

        $this->syncToExternalApi($data->toArray());

        return redirect('/admin/data-noo')
            ->with('message', 'Data berhasil diperbarui')
            ->with('mode', 'success');
    }

    public function destroy($id)
    {
        MappingIDCCustomer::findOrFail($id)->delete();

        return redirect('/admin/data-noo')
            ->with('message', 'Data berhasil dihapus')
            ->with('mode', 'success');
    }

    public function datatable(Request $request)
    {
        $query = MappingIDCCustomer::query();

        if ($request->has('sync_status') && $request->sync_status != '') {
            $query->where('sync_status', $request->sync_status);
        }

        if ($request->has('tanggal_start') && $request->tanggal_start != '') {
            $query->whereDate('created_at', '>=', Carbon::createFromFormat('Y-m-d', $request->tanggal_start));
        }

        if ($request->has('tanggal_end') && $request->tanggal_end != '') {
            $query->whereDate('created_at', '<=', Carbon::createFromFormat('Y-m-d', $request->tanggal_end));
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('created_at', function ($row) {
                return Carbon::parse($row->created_at)->format('d-m-Y');
            })
            ->addColumn('action', function ($row) {
                $retryBtn = '';
                if (in_array($row->sync_status, ['failed', 'error'])) {
                    $retryBtn = '
                    <button class="btn btn-sm btn-warning btn-retry"
                        data-url="' . route('data-noo.retry', $row->id) . '"
                        title="Retry Sync">
                        <i class="ri ri-refresh-line"></i>
                    </button>';
                }

                return '
                    <button title="Lihat Gambar" class="btn btn-sm btn-info btn-preview-image" data-id="' . $row->id . '">
                        <i class="ri ri-image-line"></i>
                    </button>
                    ' . $retryBtn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function images($id)
    {
        $row = MappingIDCCustomer::findOrFail($id);

        return response()->json([
            'img1' => resolveImageSrc($row->ImageTokoAvian),
            'img2' => resolveImageSrc($row->ImageTokoAvian1),
        ]);
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:50240',
        ], [
            'file.required' => 'File wajib diunggah',
            'file.mimes'    => 'Format file harus .xlsx atau .xls',
        ]);

        $importer = new MappingIDCCustomerImport();
        $importer->import($request->file('file'));

        return redirect('/admin/data-noo')
            ->with('message', 'Data berhasil diimport dari Excel')
            ->with('mode', 'success');
    }

    public function clearFilter()
    {
        session()->forget('mapping_idc_filter');
        return response()->json(['status' => true]);
    }

    public function downloadTemplate()
    {
        return (new MappingIDCCustomerTemplateExport())
            ->download('template_data_noo.xlsx');
    }

    private function validateData(Request $request, $id = null)
    {
        return $request->validate([
            'IDC'             => 'required|string|max:20',
            'Nama'            => 'required|string',
            'Alamat1'         => 'nullable|string',
            'Alamat2'         => 'nullable|string',
            'Kabupaten'       => 'required|string',
            'Kecamatan'       => 'required|string',
            'NIK'             => 'nullable|string',
            'Telepon'         => 'nullable|string',
            'HP'              => 'nullable|string',
            'Longitude'       => 'required|string',
            'Latitude'        => 'required|string',
            'ImageTokoAvian'  => 'nullable|string',
            'ImageTokoAvian1' => 'nullable|string',
            'KeteranganAvian' => 'nullable|string',
        ]);
    }

    private function syncToExternalApi(array $data): string
    {
        $kodeAvid = $data['IDC'] ?? null;

        try {
            $payload = collect($data)->only([
                'IDC', 'Nama', 'Alamat1', 'Alamat2', 'Kabupaten', 'Kecamatan',
                'NIK', 'Telepon', 'HP', 'Longitude', 'Latitude',
                'ImageTokoAvian', 'ImageTokoAvian1', 'KeteranganAvian',
            ])->toArray();

            $response = Http::withHeaders([
                'api-key' => config('services.tirta.token'),
            ])->post('https://tirtaapps.tirtakencana.com/utility-noo/api/tirta/insert-customer-idc', $payload);

            $body = $response->json();

            if ($response->successful()) {
                MappingIDCCustomer::where('IDC', $kodeAvid)->update([
                    'sync_status' => 'success',
                    'keterangan'  => null,
                ]);
                return 'success';
            }

            $message     = $body['message'] ?? $response->body();
            $isDuplicate = str_contains(strtolower($message), 'sudah ada');

            if (! empty($body['errors']) && is_array($body['errors'])) {
                $message = collect($body['errors'])->flatten()->implode(', ');
            }

            MappingIDCCustomer::where('IDC', $kodeAvid)->update([
                'sync_status' => $isDuplicate ? 'duplicate' : 'failed',
                'keterangan'  => $message,
            ]);

            if ($isDuplicate) {
                Log::info('syncToExternalApi: duplicate', ['idc' => $kodeAvid]);
                return 'duplicate';
            }

            Log::warning('syncToExternalApi failed', [
                'idc'     => $kodeAvid,
                'status'  => $response->status(),
                'message' => $message,
            ]);
            return 'failed';

        } catch (\Throwable $e) {
            MappingIDCCustomer::where('IDC', $kodeAvid)->update([
                'sync_status' => 'error',
                'keterangan'  => $e->getMessage(),
            ]);

            Log::error('syncToExternalApi exception', [
                'idc'   => $kodeAvid,
                'error' => $e->getMessage(),
            ]);
            return 'error';
        }
    }

    public function export(Request $request)
    {
        $syncStatus = $request->query('sync_status');

        $filename = 'export_data_noo'
        . ($syncStatus ? "_{$syncStatus}" : '')
        . '_' . now()->format('Ymd_His')
            . '.xlsx';

        return (new MappingIDCCustomerExport($syncStatus))->download($filename);
    }

    public function retry($id)
    {
        $record = MappingIDCCustomer::findOrFail($id);

        if (! in_array($record->sync_status, ['failed', 'error'])) {
            return response()->json(['message' => 'Hanya status failed/error yang bisa di-retry'], 422);
        }

        $kodeAvid = $record->IDC;
        $idSfa    = $record->id_sfa;
        $avid     = dataCustomerAvid($kodeAvid);

        if (! empty($idSfa)) {
            $customer          = dataCustomerByCustomerCode($kodeAvid);
            [$image1, $image2] = getCustImageByID($idSfa);
            $latitude          = $customer?->Latitude;
            $longitude         = $customer?->Longitude;
        } else {
            $customer  = dataCustomerByCustomerCode($kodeAvid);
            $latitude  = $customer?->Latitude ?? $customer?->latitude ?? $record->Latitude;
            $longitude = $customer?->Longitude ?? $customer?->longitude ?? $record->Longitude;
            $image1    = $record->ImageTokoAvian;
            $image2    = $record->ImageTokoAvian1;
        }

        $payload = [
            'IDC'             => $kodeAvid,
            'Nama'            => $avid?->Nama ?? $record->Nama,
            'Alamat1'         => $avid?->Alamat1 ?? $record->Alamat1,
            'Alamat2'         => $avid?->Alamat2 ?? $record->Alamat2,
            'Kabupaten'       => $avid?->Kabupaten ?? $record->Kabupaten,
            'Kecamatan'       => $avid?->Kecamatan ?? $record->Kecamatan,
            'NIK'             => $avid?->KTP ?? $record->NIK,
            'Telepon'         => $avid?->Phone ?? $record->Telepon,
            'HP'              => $avid?->HP ?? $record->HP,
            'Longitude'       => $longitude,
            'Latitude'        => $latitude,
            'ImageTokoAvian'  => $image1,
            'ImageTokoAvian1' => $image2,
            'KeteranganAvian' => $record->KeteranganAvian,
        ];

        $record->fill(array_filter($payload, fn($v) => $v !== null))->save();

        $result = $this->syncToExternalApi($payload);

        Log::info('retry finished', [
            'id'     => $id,
            'idc'    => $kodeAvid,
            'result' => $result,
        ]);

        return response()->json([
            'message' => 'Retry berhasil diproses',
            'result'  => $result,
        ]);
    }
}
