<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Datalog extends Model
{
    protected $table = 'log';

    public $timestamps = false;

    protected $fillable = [
        'log_name',
        'menu',
        'username',
        'data_init',
        'data_update',
        'created_at',
    ];

    protected $casts = [
        'data_init'   => 'array',
        'data_update' => 'array',
        'created_at'  => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(Users::class, 'username', 'username');
    }
}
