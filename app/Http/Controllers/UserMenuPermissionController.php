<?php
namespace App\Http\Controllers;

use App\Models\Program;
use App\Models\UserMenuPermission;
use App\Models\Users;
use Illuminate\Http\Request;

class UserMenuPermissionController extends Controller
{
    public function index()
    {
        $users = Users::where('aktif', 1)->orderBy('nama')->get();
        return view('permissions.menu_index', compact('users'));
    }

    public function edit($user_id)
    {
        $user = Users::findOrFail($user_id);

        $programs = Program::whereHas('userPrograms', function ($query) use ($user_id) {
            $query->where('user_id', $user_id);
        })->with('menus')->orderBy('name')->get();

        $existing = UserMenuPermission::where('user_id', $user_id)
            ->get()
            ->keyBy('menu_id');

        return view('permissions.menu_form', compact('user', 'programs', 'existing'));
    }

    public function update(Request $request, $user_id)
    {
        $user = Users::findOrFail($user_id);

        UserMenuPermission::where('user_id', $user_id)->delete();

        $permissions = $request->input('permissions', []);

        foreach ($permissions as $menu_id => $perms) {
            UserMenuPermission::create([
                'user_id'    => $user_id,
                'menu_id'    => $menu_id,
                'can_view'   => isset($perms['can_view']) ? 1 : 0,
                'can_insert' => isset($perms['can_insert']) ? 1 : 0,
                'can_update' => isset($perms['can_update']) ? 1 : 0,
                'can_delete' => isset($perms['can_delete']) ? 1 : 0,
            ]);
        }

        return redirect('/master/user-menu-permissions')
            ->with('message', "Permission menu untuk {$user->nama} berhasil disimpan.")
            ->with('mode', 'success');
    }
}
