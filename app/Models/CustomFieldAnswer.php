<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomFieldAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'pendaftaran_id',
        'custom_field_id',
        'jawaban',
    ];

    public function pendaftaran()
    {
        return $this->belongsTo(Pendaftaran::class);
    }

    public function customField()
    {
        return $this->belongsTo(CustomField::class);
    }
}
