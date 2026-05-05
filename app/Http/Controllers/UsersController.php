<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Redirect;
use Session;
use Yajra\DataTables\Facades\DataTables;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        createLog('akses_menu', 'Users');

        $levels = DB::table('users')->select('level')->distinct()->get();
        return view('users.index', compact('levels'));
    }

    public function create()
    {
        return view('users.update', [
            'data' => null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateUsers($request);

        $data = Users::create([
            'username'           => $validated['username'],
            'password'           => bcrypt($validated['password']),
            'tipe'               => $validated['tipe_user'],
            'nama'               => $validated['nama_users'],
            'email'              => $validated['email_users'],
            'level'              => $validated['level_users'],
            'cabang'             => $validated['cabang_users'],
            'aktif'              => $request->has('aktif') ? 1 : 0,
            'created_by'         => Session::get('userinfo')['username'],
            'updated_by'         => Session::get('userinfo')['username'],
            'password_expiry_at' => now()->addDays(90),
        ]);

        createLog('create_user', 'Users', null, $data->toJson());
        return redirect('/master/users')
            ->with('message', 'Data sudah disimpan')
            ->with('mode', 'success');
    }

    public function edit($id)
    {
        $data = Users::findOrFail($id);

        return view('users.update', [
            'data' => $data,
        ]);
    }

    public function update(Request $request, $id)
    {
        $data     = Users::findOrFail($id);
        $dataInit = $data->toJson();

        $validated = $this->validateUsersUpdate($request, $id);

        $data->username   = $validated['username'];
        $data->tipe       = $validated['tipe_user'];
        $data->nama       = $validated['nama_users'];
        $data->email      = $validated['email_users'];
        $data->level      = $validated['level_users'];
        $data->cabang     = $validated['cabang_users'];
        $data->aktif      = $request->has('aktif') ? 1 : 0;
        $data->updated_by = Session::get('userinfo')['username'];

        if ($request->filled('password')) {
            $data->password           = bcrypt($validated['password']);
            $data->password_expiry_at = now()->addDays(90);
        }

        $data->save();
        createLog('update_user', 'Users', $dataInit, $data->toJson());

        return redirect('/master/users')
            ->with('message', 'Data sudah diperbarui')
            ->with('mode', 'success');
    }

    public function destroy($id)
    {
        $data             = Users::findOrFail($id);
        $dataInit         = $data->toJson();
        $data->aktif      = 0;
        $data->updated_by = Session::get('userinfo')['username'];
        $data->save();

        createLog('delete_user', 'Users', $dataInit, null);

        return Redirect::to('/master/users/')
            ->with('message', 'Data sudah dihapus')
            ->with('mode', 'success');
    }

    public function show($id)
    {
        $data = Users::findOrFail($id);

        return view('users.view', compact('data'));
    }

    public function datatable(Request $request)
    {

        $data  = Users::where('aktif', 1);
        $level = $request->get('level');
        $name  = $request->get('name');

        if ($level) {
            $data->where('level', $level);
        }

        if ($name) {
            $data->where('nama', 'like', '%' . $name . '%');
        }

        return DataTables::of($data)
            ->editColumn('tipe', function ($row) {
                return $row->tipe == 'AD' ? 'ADMIN' : 'USER';
            })
            ->addColumn('action', function ($row) {
                $view = url('master/users/' . $row->id);
                $edit = url('master/users/' . $row->id . '/edit');

                $delete = '';
                if (Session::get('userinfo')['level'] === 'SUPER') {
                    $delete = "<button class='btn btn-danger btn-sm' data-bs-toggle='modal'
                    data-bs-target='#deleteModal'
                    data-url='$view'>
                    <i class='ri ri-delete-bin-2-line'></i>
                    </button>";
                }

                return "
                <a href='$view' class='btn btn-primary btn-sm'><i class='ri ri-eye-line'></i></a>
                <a href='$edit' class='btn btn-warning btn-sm'><i class='ri ri-file-edit-line'></i></a>
                $delete
                ";
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    private function validateUsers(Request $request)
    {
        return $request->validate([
            'username'     => 'required|string|max:50',
            'password'     => 'nullable|required_unless:tipe_user,AD|string|max:100',
            'tipe_user'    => 'required|in:AD,USER',
            'nama_users'   => 'required|string|max:100',
            'email_users'  => 'required|email|max:100',
            'level_users'  => 'required|string|max:50',
            'cabang_users' => 'nullable|string|max:50',
        ], [
            'password.required_unless' => 'Password wajib diisi untuk user selain AD',
            'nama_users.required'      => 'Nama wajib diisi',
            'email_users.required'     => 'Email wajib diisi',
            'email_users.email'        => 'Format email tidak valid',
            'level_users.in'           => 'Level user tidak valid',
            'cabang_users.max'         => 'Maksimal 50 karakter',
        ]);
    }

    private function validateUsersUpdate(Request $request, $id)
    {
        return $request->validate([
            'username'     => 'required|string|max:50|unique:users,username,' . $id,
            'password'     => 'nullable|string|min:6|max:100',
            'tipe_user'    => 'required|in:AD,USER',
            'nama_users'   => 'required|string|max:100',
            'email_users'  => 'required|email|max:100|unique:users,email,' . $id,
            'level_users'  => 'required|string|max:50',
            'cabang_users' => 'nullable|string|max:50',
        ], [
            'username.unique'      => 'Username sudah digunakan',
            'email_users.unique'   => 'Email sudah digunakan',
            'nama_users.required'  => 'Nama wajib diisi',
            'email_users.required' => 'Email wajib diisi',
            'email_users.email'    => 'Format email tidak valid',
            'level_users.in'       => 'Level user tidak valid',
            'cabang_users.max'     => 'Maksimal 50 karakter',
        ]);
    }

    public function clearFilter()
    {
        session()->forget('user_filter');
        return response()->json(['status' => true]);
    }
}
