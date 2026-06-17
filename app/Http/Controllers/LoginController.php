<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\LoginAttempt;
use App\Models\Users;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Session;

class LoginController extends Controller
{
    const MAX_ATTEMPTS = 3;

    private const AD_ERROR_MAP = [
        '52e' => ['type' => 'wrong_password', 'message' => 'Username atau password salah.'],
        '525' => ['type' => 'wrong_password', 'message' => 'Username atau password salah.'],
        '532' => ['type' => 'password_expired', 'message' => 'Password AD Anda telah kadaluarsa. Silahkan reset di link berikut.'],
        '773' => ['type' => 'password_expired', 'message' => 'Password AD Anda telah kadaluarsa. Silahkan reset di link berikut.'],
        '701' => ['type' => 'password_expired', 'message' => 'Password AD Anda telah kadaluarsa. Silahkan reset di link berikut.'],
        '775' => ['type' => 'account_locked', 'message' => 'Akun AD Anda terkunci. Hubungi IT Helpdesk.'],
        '533' => ['type' => 'account_locked', 'message' => 'Akun AD Anda telah dinonaktifkan. Hubungi IT Helpdesk.'],
        '530' => ['type' => 'no_access', 'message' => 'Tidak ada akses. Hubungi IT Helpdesk.'],
        '531' => ['type' => 'no_access', 'message' => 'Tidak ada akses. Hubungi IT Helpdesk.'],
        '534' => ['type' => 'no_access', 'message' => 'Tidak ada akses. Hubungi IT Helpdesk.'],
    ];

    private function parseAdError(?string $ldapError): array
    {
        if ($ldapError && preg_match('/\bdata\s+([0-9a-f]+)\b/i', $ldapError, $m)) {
            $code = strtolower($m[1]);
            if (isset(self::AD_ERROR_MAP[$code])) {
                return self::AD_ERROR_MAP[$code];
            }
        }

        return ['type' => 'wrong_password', 'message' => 'Username atau password salah.'];
    }

    private function getAttempt(string $username, string $ip): LoginAttempt
    {
        return LoginAttempt::firstOrNew([
            'username'   => $username,
            'ip_address' => $ip,
        ]);
    }

    private function recordFailedAttempt(Users $user, string $ip): void
    {
        $attempt                  = $this->getAttempt($user->username, $ip);
        $attempt->attempt_count   = ($attempt->attempt_count ?? 0) + 1;
        $attempt->last_attempt_at = now();
        $attempt->save();

        if ($attempt->attempt_count >= self::MAX_ATTEMPTS) {
            $user->password_expiry_at = now();
            $user->save();
        }
    }

    private function resetAttempt(string $username, string $ip): void
    {
        LoginAttempt::where('username', $username)
            ->where('ip_address', $ip)
            ->delete();
    }

    private function loginViaAD(Users $user, string $password): array
    {
        $conn = @ldap_connect('192.168.110.110', 389);

        if (! $conn) {
            return [
                'success' => false,
                'type'    => 'no_access',
                'message' => 'Tidak bisa terhubung ke server AD.',
            ];
        }

        ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($conn, LDAP_OPT_REFERRALS, 0);

        $bind = @ldap_bind($conn, $user->username . '@avianbrands.com', $password);

        $ldapErrno = ldap_errno($conn);
        $ldapError = ldap_error($conn);

        $diagMsg = null;
        if ($ldapErrno === 49) {
            ldap_get_option($conn, LDAP_OPT_DIAGNOSTIC_MESSAGE, $diagMsg);
        }

        ldap_close($conn);

        if ($bind) {
            return ['success' => true];
        }

        $parsed = $this->parseAdError($diagMsg ?? $ldapError);

        return [
            'success' => false,
            'type'    => $parsed['type'],
            'message' => $parsed['message'],
            'errno'   => $ldapErrno,
        ];
    }

    public function doLogin(Request $request): array
    {
        $data = [
            'status'   => false,
            'expired'  => false,
            'response' => ['message' => 'An error occurred', 'error' => []],
        ];

        $ip   = $request->ip();
        $user = Users::where('username', $request->username)->where('aktif', 1)->first();

        if (! $user) {
            $data['response']['error'] = [['Username / Password salah.']];
            return $data;
        }

        if ($user->tipe === 'AD') {
            $result = $this->loginViaAD($user, $request->password);

            if (! $result['success']) {
                $data['ad_error_type']     = $result['type'] ?? 'wrong_password';
                $data['response']['error'] = [[$result['message']]];
                return $data;
            }
        } else {
            $attempt = $this->getAttempt($user->username, $ip);

            if (($attempt->attempt_count ?? 0) >= self::MAX_ATTEMPTS) {
                $data['expired']           = true;
                $data['user']              = $user;
                $data['response']['error'] = [['Akun terkunci karena 3x salah password. Silahkan ganti password terlebih dahulu.']];
                return $data;
            }

            if (! Hash::check($request->password, $user->password)) {
                $this->recordFailedAttempt($user, $ip);
                $sisa = self::MAX_ATTEMPTS - (($attempt->attempt_count ?? 0) + 1);
                $msg  = 'Username / Password salah.' . ($sisa > 0
                        ? " Sisa percobaan: {$sisa}x."
                        : ' Akun terkunci, silahkan ganti password.');
                $data['response']['error'] = [[$msg]];
                return $data;
            }

            $this->resetAttempt($user->username, $ip);

            if ($user->isPasswordExpired()) {
                $data['expired']           = true;
                $data['user']              = $user;
                $data['response']['error'] = [['Password anda sudah kadaluarsa. Silahkan ganti password.']];
                return $data;
            }
        }

        $this->resetAttempt($user->username, $ip);

        $data['status']                       = true;
        $data['response']['data']['userinfo'] = [
            'id'       => $user->id,
            'username' => $user->username,
            'level'    => $user->level,
            'nama'     => $user->nama,
            'email'    => $user->email,
            'tipe'     => $user->tipe,
            'cabang'   => $user->cabang,
        ];

        return $data;
    }

    public function index(Request $request)
    {
        if ($request->isMethod('GET')) {
            if (Session::get('userinfo')) {
                return redirect('dashboard');
            }
            return view('login');
        }

        $response = $this->doLogin($request);

        if ($response['status']) {
            Session::put('userinfo', $response['response']['data']['userinfo']);
            return new JsonResponse(['status' => true, 'message' => 'Login Success'], 200);
        }

        if (! empty($response['expired']) && ! empty($response['user'])) {
            return new JsonResponse([
                'status'   => false,
                'expired'  => true,
                'username' => $response['user']->username,
                'message'  => $response['response']['error'],
            ], 200);
        }

        return new JsonResponse([
            'status'        => false,
            'ad_error_type' => $response['ad_error_type'] ?? null,
            'message'       => $response['response']['error'],
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect('/login');
    }

    public function changePasswordForm(Request $request)
    {
        $username = $request->query('username', '');
        return view('auth.change_password', compact('username'));
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'username'                  => 'required',
            'password_new'              => 'required|min:6|confirmed',
            'password_new_confirmation' => 'required',
        ], [
            'password_new.confirmed' => 'Konfirmasi password tidak cocok.',
            'password_new.min'       => 'Password minimal 6 karakter.',
        ]);

        $user = Users::where('username', $request->username)
            ->where('aktif', 1)
            ->where('tipe', '!=', 'AD')
            ->firstOrFail();

        $user->password           = bcrypt($request->password_new);
        $user->password_expiry_at = now()->addDays(90);
        $user->updated_by         = $request->username;
        $user->save();

        LoginAttempt::where('username', $user->username)->delete();

        return redirect('/login')->with('message_success', 'Password berhasil diubah. Silahkan login kembali.');
    }
}
