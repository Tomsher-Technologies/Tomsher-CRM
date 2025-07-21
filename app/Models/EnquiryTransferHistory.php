<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnquiryTransferHistory extends Model
{
    use HasFactory;

    protected $fillable = ['enquiry_id', 'transferred_by', 'transferred_to'];

    public function enquiry() {
        return $this->belongsTo(Enquiry::class);
    }

    public function fromUser() {
        return $this->belongsTo(User::class, 'transferred_by');
    }

    public function toUser() {
        return $this->belongsTo(User::class, 'transferred_to');
    }
}
