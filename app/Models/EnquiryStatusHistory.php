<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnquiryStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = ['enquiry_id', 'status', 'status_date', 'comment', 'submitted_cost', 'approved_cost', 'changed_by'];

    public function enquiry()
    {
        return $this->belongsTo(Enquiry::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function proposalItems()
    {
        return $this->hasMany(EnquiryProposalItem::class,'status_history_id');
    }
}
