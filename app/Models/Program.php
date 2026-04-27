<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{

    protected $table = 'programs';

    protected $fillable = [
        'name',
        'url',
        "photo_url",
        'created_at',
    ];

    public $timestamps = false;

    public function menus()
    {
        return $this->hasMany(Menu::class, 'program_id');
    }

    public function userPrograms()
    {
        return $this->hasMany(UserProgram::class, 'program_id');
    }

    public function getPhotoUrlAttribute($value)
    {
        if ($value && file_exists(storage_path('app/public/' . $value))) {
            return asset('storage/' . $value);
        }
        return null;
    }

    public function getPhotoPathAttribute()
    {
        return $this->attributes['photo_url'] ?? null;
    }
}
