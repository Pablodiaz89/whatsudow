<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];





    public function company()
    {
        return $this->hasOne(Company::class);
    }

    public function document()
    {
        return $this->hasOne(Document::class);
    }

    public function description()
    {
        return $this->hasOne(Description::class);
    }

    public function phone()
    {
        return $this->hasOne(Phone::class);
    }

    public function categories() // un usuario puede pertenecer a varias categorias
    {
        return $this->belongsToMany(Category::class, 'category_user')->withTimestamps();
    }

    public function services() // un usuario tiene varios servicios 
    {
        return $this->hasMany(Service::class);
    }

    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }
    
    public function locations()
    {
        return $this->belongsTo(Location::class);
    }

    public function availabilities()
    {
        return $this->hasMany(Availability::class);
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }
}
