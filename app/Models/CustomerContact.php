<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerContact extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id', 'name', 'email', 'landline_number', 'mobile_number', 'whatsapp_number', 'designation', 'is_primary'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
