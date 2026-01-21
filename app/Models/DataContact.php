<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataContact extends Model
{
    use HasFactory;

    protected $fillable = ['data_id', 'name', 'email', 'landline_number', 'mobile_number', 'whatsapp_number', 'designation', 'is_primary'];

    public function data()
    {
        return $this->belongsTo(Data::class);
    }
}
