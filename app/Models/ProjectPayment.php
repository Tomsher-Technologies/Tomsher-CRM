<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id', 'payment_title', 'expected_date', 'amount', 'percentage', 'tax', 'total_amount', 'received_amount', 'received_tax', 'received_total_amount', 'method', 'status', 'received_date', 'created_at'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
