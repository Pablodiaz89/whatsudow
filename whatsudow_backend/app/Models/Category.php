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
    ];


    public function users() // una categoria tiene varios usuarios
    {
        return $this->belongsToMany(User::class, 'category_user')->withTimestamps();
    }

    public function services() // una categoria pertenece a varios servicios
    {
        return $this->hasMany(Service::class);
    }
}
