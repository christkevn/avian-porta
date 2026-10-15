<?php
namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\UserProgram;
use App\Models\Users;
use Illuminate\Http\Request;

class UserProgramController extends Controller
{
    public function index(Request $request)
    {
        createLog('akses_menu', 'Permission Program');
        $key = 'perm_program_filter';
        if ($request->boolean('clear')) {
            session()->forget($key);
            return redirect(url()->current());
        }
        if ($request->has('filter')) {
            session([$key => ['level' => $request->input('level'), 'name' => $request->input('name')]]);
            return redirect(url()->current());
        }
        $filter = session($key, []);
        $levels = Users::where('aktif', 1)->whereNotNull('level')->distinct()->orderBy('level')->pluck('level');
        $users  = Users::where('aktif', 1)
            ->when(!empty($filter['level']), fn($q) => $q->where('level', $filter['level']))
            ->when(!empty($filter['name']), fn($q) => $q->where('nama', 'like', '%' . $filter['name'] . '%'))
            ->orderBy('nama')->get();

        $userPrograms = UserProgram::join('programs', 'programs.id', '=', 'user_programs.program_id')
            ->whereIn('user_programs.user_id', $users->pluck('id'))
            ->orderBy('programs.name')
            ->get(['user_programs.user_id', 'programs.name'])
            ->groupBy('user_id')
            ->map(fn($rows) => $rows->pluck('name')->toArray());

        return view('permissions.program_index', compact('users', 'userPrograms', 'levels', 'filter'));
    }

    public function edit($user_id)
    {
        $user     = Users::findOrFail($user_id);
        $programs = Program::orderBy('name')->get();
        $assigned = UserProgram::where('user_id', $user_id)->pluck('program_id')->toArray();

        return view('permissions.program_form', compact('user', 'programs', 'assigned'));
    }

    public function update(Request $request, $user_id)
    {
        $user = Users::findOrFail($user_id);

        $dataInit = UserProgram::where('user_id', $user_id)->get()->toJson();

        UserProgram::where('user_id', $user_id)->delete();

        $programIds = $request->input('program_ids', []);
        foreach ($programIds as $program_id) {
            UserProgram::create([
                'user_id'    => $user_id,
                'program_id' => $program_id,
            ]);
        }

        $dataUpdate = UserProgram::where('user_id', $user_id)->get()->toJson();

        createLog(
            'update_permission_program',
            'Permission Program',
            $dataInit,
            $dataUpdate
        );

        return redirect('/master/user-program-permissions')
            ->with('message', "Program untuk {$user->nama} berhasil disimpan.")
            ->with('mode', 'success');
    }
}
