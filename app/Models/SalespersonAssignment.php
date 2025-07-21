<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalespersonAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'old_sales_person_id',
        'new_sales_person_id',
        'enquiry_id',
        'changed_by'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function enquiry()
    {
        return $this->belongsTo(Enquiry::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function oldSalesPerson(){
        return $this->belongsTo(User::class, 'old_sales_person_id');
    }

    public function newSalesPerson(){
        return $this->belongsTo(User::class, 'new_sales_person_id');
    }
}
