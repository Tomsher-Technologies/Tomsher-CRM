<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'enquiry_code','customer_id', 'enquiry_date', 'status', 'enquiry_source_id','project_type_id', 'project_details', 'comments','added_by', 'updated_by','updated_at','submitted_cost', 'approved_cost', 'project_created', 'owner_id'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function source()
    {
        return $this->belongsTo(EnquirySource::class, 'enquiry_source_id');
    }

    public function projectTypes()
    {
        return $this->belongsToMany(ProjectType::class, 'enquiry_project_types');
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function statusHistories()
    {
        return $this->hasMany(EnquiryStatusHistory::class);
    }

    public function transferHistories() {
        return $this->hasMany(EnquiryTransferHistory::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function followups() {
        return $this->hasMany(EnquiryFollowup::class);
    }

    public function proposalItems()
    {
        return $this->hasMany(EnquiryProposalItem::class);
    }
}
