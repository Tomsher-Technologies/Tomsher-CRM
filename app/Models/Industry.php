<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Industry extends Model
{
    use HasFactory;

    protected $fillable = ['name','parent_id','status','created_by','updated_by'];

    public function parent()
    {
        return $this->belongsTo(Industry::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Industry::class, 'parent_id');
    }
}
