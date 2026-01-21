<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = ['data_id', 'status', 'status_date', 'comment', 'followup_date', 'changed_by'];

    public function data()
    {
        return $this->belongsTo(Data::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

}
