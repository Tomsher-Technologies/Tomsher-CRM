<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = ['customer_code', 'sales_person', 'company_name', 'company_email', 'industry_id', 'company_address', 'company_country', 'emirate', 'website_link', 'ntc', 'google_location', 'is_active'];

    public function contacts()
    {
        return $this->hasMany(CustomerContact::class);
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

    public function assignmentHistory()
    {
        return $this->hasMany(SalespersonAssignment::class);
    }
}
