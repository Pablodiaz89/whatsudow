<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'sender_name',
        'sender_email',
        'sender_telefono',
        'addresse_id',
        'parent_id',
        'title',
        'event_date',
        'location_id',
        'description',
        'message',
        'read',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function addressee()
    {
        return $this->belongsTo(User::class, 'addresse_id');
    }

    public function parent()
    {
        return $this->belongsTo(Message::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Message::class, 'parent_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}

