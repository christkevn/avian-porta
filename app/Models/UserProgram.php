<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProgram extends Model
{

    protected $table   = 'user_programs';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'program_id',
    ];

    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }
}
