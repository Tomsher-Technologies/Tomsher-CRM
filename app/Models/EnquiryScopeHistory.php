<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnquiryScopeHistory extends Model
{
    protected $table = 'enquiry_scope_histories';

    protected $fillable = [
        'scope_of_work_id',
        'scope_content',
        'edited_by'
    ];

    public function scope()
    {
        return $this->belongsTo(EnquiryScopeOfWork::class, 'scope_of_work_id');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'edited_by');
    }
}
