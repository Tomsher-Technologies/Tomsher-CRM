<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnquiryFollowup extends Model
{
    use HasFactory;

    protected $fillable = [
        'enquiry_id', 'followup_type', 'sub_type', 'followup_time', 'subject', 'location', 'status','created_by','updated_by','enquiry_status','post_comment','followup_from','followup_to'
    ];

    public function enquiry() {
        return $this->belongsTo(Enquiry::class);
    }

    public function added_by() {
        return $this->belongsTo(User::class,'created_by');
    }

    public function edited_by() {
        return $this->belongsTo(User::class,'updated_by');
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'enquiry_followup_participants', 'followup_id', 'user_id');
    }
}

