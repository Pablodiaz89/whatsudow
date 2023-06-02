<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
