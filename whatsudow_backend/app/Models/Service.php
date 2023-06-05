<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Image;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price'       
    ];

    public function user()  // un servicio pertene a un usuario
    {
        return $this->belongsTo(User::class);
    }

    public function locations()
    {
        return $this->belongsToMany(Location::class, 'location_service')->withTimestamps();
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }
}
