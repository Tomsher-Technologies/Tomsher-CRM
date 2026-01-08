<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnquiryScopeComment extends Model
{
    protected $table = 'enquiry_scope_comments';

    protected $fillable = [
        'scope_of_work_id',
        'comment',
        'is_sales_team',
        'commented_by'
    ];

    public function scope()
    {
        return $this->belongsTo(EnquiryScopeOfWork::class, 'scope_of_work_id');
    }

    public function commenter()
    {
        return $this->belongsTo(User::class, 'commented_by');
    }
}
