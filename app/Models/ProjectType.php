<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectType extends Model
{
    use HasFactory;

    protected $fillable = ['name','status','created_by','updated_by'];
    
    public function enquiries()
    {
        return $this->belongsToMany(Enquiry::class, 'enquiry_project_types');
    }
}
