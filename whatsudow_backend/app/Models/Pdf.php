<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pdf extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_id',
        'pdf_id',
    ];

    public function file()
    {
        return $this->belongsTo(File::class);
    }

    public function pdf()
    {
        return $this->belongsTo(Session::class);
    }
}
