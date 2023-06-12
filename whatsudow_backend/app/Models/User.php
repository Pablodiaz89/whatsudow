<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Avatar;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Models\Permission;

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



    public function services() // un usuario tiene varios servicios 
    {
        return $this->hasMany(Service::class);
    }

    public function avatar()
    {
        return $this->hasOne(Avatar::class);
    }

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

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function galleries()
    {
        return $this->hasMany(Gallery::class);
    }
    
    public function budgets()
    {
        return $this->hasMany(Budget::class);
    }

    public function phone()
    {
        return $this->hasOne(Phone::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'addressee_id');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function locations()
    {
        return $this->belongsTo(Location::class);
    }

    public function availabilities()
    {
        return $this->hasMany(Availability::class);
    }
}
