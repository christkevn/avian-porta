<?php
namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\UserProgram;
use App\Models\Users;
use Illuminate\Http\Request;

class UserProgramController extends Controller
{
    public function index()
    {
        $users = Users::where('aktif', 1)->orderBy('nama')->get();
        return view('permissions.program_index', compact('users'));
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

        UserProgram::where('user_id', $user_id)->delete();

        $programIds = $request->input('program_ids', []);
        foreach ($programIds as $program_id) {
            UserProgram::create([
                'user_id'    => $user_id,
                'program_id' => $program_id,
            ]);
        }

        return redirect('/master/user-program-permissions')
            ->with('message', "Program untuk {$user->nama} berhasil disimpan.")
            ->with('mode', 'success');
    }
}
