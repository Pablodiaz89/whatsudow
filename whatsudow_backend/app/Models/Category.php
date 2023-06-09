<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'icon',
        'user_id',
    ];


    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function services() // una categoria pertenece a varios servicios
    {
        return $this->hasMany(Service::class);
    }
}
