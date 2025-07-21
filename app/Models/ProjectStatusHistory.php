<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectStatusHistory extends Model
{
    use HasFactory;

    public $timestamps = false; // We use 'changed_at' instead
    protected $fillable = [
        'project_id', 'old_status', 'new_status', 'changed_by', 'changed_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}
