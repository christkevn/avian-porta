<?php
namespace App\Http\Controllers;

use App\Models\Program;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'program_id' => 'nullable|integer',
            'from'       => 'nullable|date',
            'to'         => 'nullable|date|after_or_equal:from',
        ]);

        [$from, $to] = $this->range($request);

        $programs = Program::whereNotNull('db_database')
            ->where('db_database', '!=', '')
            ->orderBy('name')
            ->get();

        $selected = $request->filled('program_id')
            ? $programs->where('id', (int) $request->program_id)
            : $programs;

        $rows   = collect();
        $errors = [];

        foreach ($selected as $program) {
            try {
                $result = DB::connection($this->connect($program))
                    ->table($program->db_log_table ?: 'log')
                    ->selectRaw('menu, COUNT(*) AS total, MAX(created_at) AS terakhir')
                    ->whereBetween('created_at', [$from, $to])
                    ->groupBy('menu')
                    ->get();

                foreach ($result as $row) {
                    $row->program_id   = $program->id;
                    $row->program_name = $program->name;
                    $rows->push($row);
                }
            } catch (\Throwable $e) {
                report($e);
                $errors[] = $program->name . ': database tidak bisa diakses / tabel log tidak ada';
            }
        }

        createLog('akses_menu', 'Laporan');

        return view('reports.index', [
            'rows'      => $rows->sortByDesc('total')->values(),
            'errors'    => $errors,
            'programs'  => $programs,
            'programId' => $request->program_id,
            'from'      => $from->toDateString(),
            'to'        => $to->toDateString(),
            'totalAll'  => $rows->sum('total'),
        ]);
    }

    public function detail(Request $request)
    {
        $request->validate([
            'program_id' => 'required|integer|exists:programs,id',
            'menu'       => 'nullable|string|max:500',
            'from'       => 'nullable|date',
            'to'         => 'nullable|date|after_or_equal:from',
        ]);

        [$from, $to] = $this->range($request);

        $program = Program::whereNotNull('db_database')
            ->where('db_database', '!=', '')
            ->findOrFail($request->program_id);

        $actions = DB::connection($this->connect($program))
            ->table($program->db_log_table ?: 'log')
            ->selectRaw('log_name, COUNT(*) AS total, MAX(created_at) AS terakhir')
            ->where('menu', $request->menu)
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('log_name')
            ->orderByDesc('total')
            ->get();

        return response()->json(['actions' => $actions]);
    }

    private function range(Request $request): array
    {
        $from = $request->filled('from') ? Carbon::parse($request->from) : now()->startOfMonth();
        $to   = $request->filled('to') ? Carbon::parse($request->to) : now();

        return [$from->startOfDay(), $to->endOfDay()];
    }

    private function connect(Program $program): string
    {
        $name = 'report_' . $program->id;
        $base = config('database.connections.' . config('database.default'));

        config(["database.connections.$name" => array_merge($base, ['database' => $program->db_database])]);
        DB::purge($name);

        return $name;
    }
}
