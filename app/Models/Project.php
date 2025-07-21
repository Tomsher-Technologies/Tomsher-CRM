<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['enquiry_id', 'customer_id', 'project_type', 'project_name', 'start_date', 'internal_deadline', 'client_deadline', 'status', 'project_total_cost', 'comment', 'created_by', 'updated_by', 'updated_at','paid_amount', 'pending_amount'];
    
    public function enquiry()
    {
        return $this->belongsTo(Enquiry::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function payments()
    {
        return $this->hasMany(ProjectPayment::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(ProjectStatusHistory::class);
    }

    public function technologies()
    {
        return $this->belongsToMany(Technologies::class, 'project_technology', 'project_id', 'technology_id');
    }
}
