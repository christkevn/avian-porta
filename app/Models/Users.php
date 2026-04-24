<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Users extends Model
{
    use Notifiable;
    //
    protected $table = 'users';

    protected $fillable = [
        'username',
        'password',
        'tipe',
        'nama',
        'email',
        'level',
        'cabang',
        'aktif',
        'updated_by',
        'created_by',
        'password_expiry_at',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    protected $casts = [
        'password_expiry_at' => 'datetime',
    ];

    public function menuPermissions()
    {
        return $this->hasMany(UserMenuPermission::class, 'user_id');
    }

    public function programs()
    {
        return $this->hasMany(UserProgram::class, 'user_id');
    }

    public function isPasswordExpired(): bool
    {
        if ($this->tipe === 'AD') {
            return false;
        }
        if (is_null($this->password_expiry_at)) {
            return false;
        }
        return now()->greaterThan($this->password_expiry_at);
    }
    public function userPrograms()
    {
        return $this->hasMany(UserProgram::class, 'user_id');
    }

    public function getAvailablePrograms()
    {
        if ($this->level === 'SUPER') {
            return Program::orderBy('name')->get();
        }

        return $this->userPrograms()
            ->with('program')
            ->get()
            ->pluck('program')
            ->filter();
    }

}
