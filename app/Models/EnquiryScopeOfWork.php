<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnquiryScopeOfWork extends Model
{
    protected $table = 'enquiry_scopes_of_work';

    protected $fillable = [
        'enquiry_id',
        'title',
        'scope_content',
        'sales_comment',
        'scope_comment',
        'status',
        'created_by',
        'updated_by'
    ];

    public function enquiry()
    {
        return $this->belongsTo(Enquiry::class);
    }

    public function histories()
    {
        return $this->hasMany(EnquiryScopeHistory::class, 'scope_of_work_id')
                    ->latest();
    }

    public function comments()
    {
        return $this->hasMany(EnquiryScopeComment::class, 'scope_of_work_id')
                    ->latest();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
