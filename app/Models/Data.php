<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Data extends Model
{
    use HasFactory;

    protected $table = 'datas';

    protected $fillable = ['data_code', 'entry_date', 'status', 'requirement', 'sales_person', 'company_name', 'company_email', 'industry_id', 'company_address', 'company_country', 'emirate', 'website_link', 'google_location', 'is_active','last_updated', 'next_followup','source_id','last_comment'];

    public function contacts()
    {
        return $this->hasMany(DataContact::class);
    }

     public function statusHistories()
    {
        return $this->hasMany(DataStatusHistory::class);
    }

    public function source()
    {
        return $this->belongsTo(EnquirySource::class, 'source_id');
    }


    public function industry()
    {
        return $this->belongsTo(Industry::class);
    }

    public function uae_emirate()
    {
        return $this->belongsTo(Emirate::class,'emirate');
    }

    public function country()
    {
        return $this->belongsTo(Country::class,'company_country');
    }

    public function getMainContactAttribute()
    {
        return $this->contacts->firstWhere('is_primary', 1) ?? $this->contacts->first();
    }

    public function projects(){
        return $this->hasMany(Project::class);
    }

    public function enquiries(){
        return $this->hasMany(Enquiry::class);
    }

    public function sale_person()
    {
        return $this->belongsTo(User::class,'sales_person');
    }

}
