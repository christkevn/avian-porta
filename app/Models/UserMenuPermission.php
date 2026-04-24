<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMenuPermission extends Model
{

    protected $table   = 'user_menu_permissions';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'menu_id',
        'can_view',
        'can_insert',
        'can_update',
        'can_delete',
    ];

    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id');
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }
}
