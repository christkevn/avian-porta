<?php
namespace App\Http\Controllers;

use App\Models\LoginAttempt;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class LockedUserController extends Controller
{
    public function index()
    {
        createLog('akses_menu', 'Locked Users');

        return view('locked_users.index');
    }

    public function datatable()
    {
        $query = LoginAttempt::join('users', 'users.username', '=', 'login_attempts.username')
            ->where('users.tipe', '!=', 'AD')
            ->where('login_attempts.attempt_count', '>=', LoginController::MAX_ATTEMPTS)
            ->where('login_attempts.last_attempt_at', '>', now()->subDay())
            ->select('login_attempts.*', 'users.nama');

        return DataTables::of($query)
            ->addColumn('locked_until', fn($r) => $r->last_attempt_at->copy()->addMinutes(LoginController::LOCK_MINUTES)->format('d-m-Y H:i:s'))
            ->addColumn('status', function ($r) {
                $locked = $r->last_attempt_at->copy()->addMinutes(LoginController::LOCK_MINUTES)->isFuture();
                return $locked
                    ? "<span class='badge rounded-pill bg-label-danger'>Terkunci</span>"
                    : "<span class='badge rounded-pill bg-label-secondary'>Sudah terbuka</span>";
            })
            ->addColumn('action', function ($r) {
                $u = e($r->username);
                return "<button class='btn btn-warning btn-sm btn-unlock' data-username='{$u}'>Unlock</button>
            <button class='btn btn-danger btn-sm btn-reset' data-username='{$u}'>Reset Password</button>";
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function unlock(Request $request)
    {
        $request->validate(['username' => 'required|string|max:50']);

        $user = Users::where('username', $request->username)->where('tipe', '!=', 'AD')->firstOrFail();

        LoginAttempt::where('username', $user->username)->delete();

        $user->password_expiry_at = now();
        $user->updated_by         = session('userinfo.username');
        $user->save();

        createLog('unlock_user', 'Users', null, $user->toJson());

        return response()->json(['status' => true]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate(['username' => 'required|string|max:50']);

        $user = Users::where('username', $request->username)->where('tipe', '!=', 'AD')->firstOrFail();

        $temp = Str::password(10, symbols: false);

        $user->password           = bcrypt($temp);
        $user->password_expiry_at = now();
        $user->updated_by         = session('userinfo.username');
        $user->save();

        LoginAttempt::where('username', $user->username)->delete();

        createLog('reset_password_user', 'Users', null, json_encode(['username' => $user->username]));

        return response()->json(['status' => true, 'temp_password' => $temp]);
    }
}
