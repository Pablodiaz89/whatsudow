<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    // Relación con el modelo Service (relación muchos a muchos)
    public function services()
    {
        return $this->belongsToMany(Service::class, 'location_service')->withTimestamps();
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'localizacion_id');
    }
}
