<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{

    protected $table   = 'login_attempts';
    public $timestamps = false;

    protected $fillable = [
        'username',
        'ip_address',
        'attempt_count',
        'last_attempt_at',
    ];

    protected $dates = ['last_attempt_at'];
}
