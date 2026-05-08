<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\UserProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $isSuperAdmin = isSuperAdmin();
        $userId       = getUserID();
        $programs     = collect();

        if ($isSuperAdmin) {
            $programs = Program::orderBy('name')->get();
        } elseif ($userId) {
            $programs = UserProgram::where('user_id', $userId)
                ->with('program')
                ->get()
                ->pluck('program')
                ->filter();
        }

        return view('dashboard', compact('programs', 'isSuperAdmin'));
    }
    public function accessProgram($id)
    {
        $program = Program::findOrFail($id);

        createLog(
            'Access Program',
            'Dashboard',
            $program->name
        );

        $url = Str::startsWith($program->url, 'http')
            ? $program->url
            : url($program->url);

        return redirect($url);
    }
}
