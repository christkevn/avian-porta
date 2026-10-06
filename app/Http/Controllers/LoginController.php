<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\LoginAttempt;
use App\Models\Users;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Session;

class LoginController extends Controller
{
    const MAX_ATTEMPTS = 3;
    const LOCK_MINUTES = 15;

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

    private function isAllowedRedirect(string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST);
        return $host && str_ends_with($host, '.avianbrands.com');
    }

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

    private function getAttempt(string $username): LoginAttempt
    {
        return LoginAttempt::firstOrNew(['username' => $username]);
    }

    private function isLocked(LoginAttempt $attempt): bool
    {
        return ($attempt->attempt_count ?? 0) >= self::MAX_ATTEMPTS
        && $attempt->last_attempt_at
        && $attempt->last_attempt_at->gt(now()->subMinutes(self::LOCK_MINUTES));
    }

    private function recordFailedAttempt(string $username, string $ip): void
    {
        $attempt = $this->getAttempt($username);

        if ($attempt->last_attempt_at && $attempt->last_attempt_at->lte(now()->subMinutes(self::LOCK_MINUTES))) {
            $attempt->attempt_count = 0;
        }

        $attempt->ip_address      = $ip;
        $attempt->attempt_count   = ($attempt->attempt_count ?? 0) + 1;
        $attempt->last_attempt_at = now();
        $attempt->save();
    }
    private function resetAttempt(string $username): void
    {
        LoginAttempt::where('username', $username)->delete();
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

        $ip       = $request->ip();
        $username = Str::limit(trim((string) $request->username), 50, '');

        if ($this->isLocked($this->getAttempt($username))) {
            $data['response']['error'] = [['Terlalu banyak percobaan gagal. Coba lagi dalam ' . self::LOCK_MINUTES . ' menit atau hubungi admin.']];
            return $data;
        }

        $user = Users::where('username', $username)->where('aktif', 1)->first();

        if ($user && $user->tipe === 'AD') {
            $result = $this->loginViaAD($user, (string) $request->password);

            if (! $result['success']) {
                $data['ad_error_type']     = $result['type'] ?? 'wrong_password';
                $data['response']['error'] = [[$result['message']]];
                return $data;
            }
        } else {
            if (! $user || ! Hash::check((string) $request->password, $user->password)) {
                $this->recordFailedAttempt($username, $ip);
                $data['response']['error'] = [['Username / Password salah.']];
                return $data;
            }

            if ($user->isPasswordExpired()) {
                $this->resetAttempt($username);
                $request->session()->put('pwd_change_user_id', $user->id);
                $data['expired']           = true;
                $data['response']['error'] = [['Password anda sudah kadaluarsa. Silahkan ganti password.']];
                return $data;
            }
        }

        $this->resetAttempt($username);

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
                $redirectUri = $request->query('redirect_uri');
                \Log::info('Redirect URI: ' . $redirectUri);
                $target = ($redirectUri && $this->isAllowedRedirect($redirectUri))
                    ? $redirectUri
                    : url('dashboard');
                return redirect($target);
            }
            return view('login');
        }

        $response = $this->doLogin($request);

        if ($response['status']) {
            Session::put('userinfo', $response['response']['data']['userinfo']);

            $redirectUri = $request->input('redirect_uri');
            \Log::info('Redirect URI: ' . $redirectUri);

            $target = ($redirectUri && $this->isAllowedRedirect($redirectUri))
                ? $redirectUri
                : url('dashboard');

            return new JsonResponse([
                'status'       => true,
                'message'      => 'Login Success',
                'redirect_uri' => $target,
            ], 200);
        }

        if (! empty($response['expired'])) {
            return new JsonResponse([
                'status'  => false,
                'expired' => true,
                'message' => $response['response']['error'],
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

    private function passwordChangeUser(Request $request): ?Users
    {
        $id = $request->session()->get('pwd_change_user_id') ?? Session::get('userinfo.id');

        return $id
            ? Users::where('id', $id)->where('aktif', 1)->where('tipe', '!=', 'AD')->first()
            : null;
    }

    public function changePasswordForm(Request $request)
    {
        $user = $this->passwordChangeUser($request);

        if (! $user) {
            return redirect('/login');
        }

        $username = $user->username;
        return view('auth.change_password', compact('username'));
    }

    public function changePassword(Request $request)
    {
        $user = $this->passwordChangeUser($request);

        if (! $user) {
            return redirect('/login');
        }

        $request->validate([
            'password_new' => ['required', 'confirmed', Password::min(6)->mixedCase()->numbers()->symbols()],
        ], [
            'password_new.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user->password           = bcrypt($request->password_new);
        $user->password_expiry_at = now()->addDays(90);
        $user->updated_by         = $user->username;
        $user->save();

        $this->resetAttempt($user->username);
        $request->session()->forget('pwd_change_user_id');

        return redirect('/login')->with('message_success', 'Password berhasil diubah. Silahkan login kembali.');
    }
}
