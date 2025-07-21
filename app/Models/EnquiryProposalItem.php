<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnquiryProposalItem extends Model
{
    use HasFactory;

    protected $fillable = ['enquiry_id', 'title', 'internal_days', 'client_days', 'cost', 'selected','status_history_id'];

    // Define the inverse relationship with Enquiry
    public function enquiry()
    {
        return $this->belongsTo(Enquiry::class);
    }

    public function status_history()
    {
        return $this->belongsTo(EnquiryStatusHistory::class,'status_history_id');
    }
}
