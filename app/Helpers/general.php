<?php

use App\Models\Datalog;
use App\Models\Setting;
use App\Models\Users;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

function formatDate($date, $format = 'd M Y')
{
    if (! $date) {
        return '-';
    }

    return Carbon::parse($date)->format($format);
}

function createLog($logname, $menu, $dataInit = null, $dataUpdate = null)
{
    $userinfo = getUserLogin();

    Datalog::create([
        'log_name'    => $logname,
        'menu'        => $menu,
        'username'    => $userinfo['username'],
        'data_init'   => $dataInit,
        'data_update' => $dataUpdate,
    ]);
}

function redirectBackIfNotSuperAdmin()
{
    if (! isSuperAdmin()) {
        abort(403, 'Unauthorized access');
    }
}

function getEmailDsdTirta($area)
{
    $area = strtolower(trim($area));

    if (is_numeric($area) && intval($area) < 10) {
        $area = str_pad($area, 2, '0', STR_PAD_LEFT);
    }

    $dsdUsername = 'dsd-' . $area;

    return DB::connection('WebApp')
        ->table('lntr_users')
        ->where('username', $dsdUsername)
        ->value('email');
}

function getData($column)
{
    $setting = Setting::where('name', $column)->get();
    return $setting[0]->text;
}

function userlevel($column)
{
    $text = "";
    if ($column == "SUPER") {
        $text = "Superadmin";
    } else if ($column == "USER") {
        $text = "Umum";
    } else if ($column == "ADMIN") {
        $text = "Admin";
    }

    return $text;
}

function status($column)
{
    $text  = "";
    $label = "";
    if ($column == "1") {
        $text  = "Aktif";
        $label = "primary";
    } else
    if ($column == "2") {
        $text  = "Nonaktif";
        $label = "warning";

    }
    return "<span class='badge rounded-pill  bg-label-" . $label . "'>" . $text . "</span>";
}

function activemenu($menu, $isParent = false)
{
    if (request()->is($menu) || request()->is($menu . '/*')) {
        return $isParent ? 'active open' : 'active';
    }

    return '';
}

function getUserLogin()
{
    $userinfo = Session::get('userinfo');

    return $userinfo;
}

function getUserID()
{
    $userinfo = getUserLogin();
    $user     = Users::where('username', $userinfo['username'])->first();
    return $user ? $user->id : null;
}
function isSuperAdmin()
{
    $userinfo = getUserLogin();

    return (
        $userinfo &&
        isset($userinfo['level']) &&
        $userinfo['level'] === 'SUPER' &&
        isset($userinfo['username']) &&
        str_contains(strtolower($userinfo['username']), 'it')
    );
}

function isAdmin()
{
    $userinfo = getUserLogin();
    return ($userinfo && isset($userinfo['level']) && in_array($userinfo['level'], ['ADMIN']));
}
