<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{

    protected $table   = 'menus';
    public $timestamps = false;

    protected $fillable = [
        'program_id',
        'name',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function permissions()
    {
        return $this->hasMany(UserMenuPermission::class, 'menu_id');
    }
}
