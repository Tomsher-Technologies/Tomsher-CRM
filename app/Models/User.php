<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'followup_mail_status',
        'followup_cc',
        'password',
        'reporting_to_id',
        'manager_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function recentlyViewedProducts()
    {
        return $this->hasMany(RecentlyViewedProduct::class);
    }

    public function staff()
    {
        return $this->hasOne(Staff::class);
    }

    public function followupParticipations()
    {
        return $this->belongsToMany(EnquiryFollowup::class, 'enquiry_followup_participants', 'user_id', 'followup_id');
    }

    public function reportingTo()
    {
        return $this->belongsTo(User::class, 'reporting_to_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function subordinates()
    {
        return $this->hasMany(User::class, 'reporting_to_id');
    }

    public function managedUsers()
    {
        return $this->hasMany(User::class, 'manager_id');
    }

    /**
     * Recursively fetch all subordinate IDs for this user.
     */
    public function getSubordinateIds($visited = [])
    {
        $visited[] = $this->id;

        $subordinates = User::where(function($query) {
            $query->where('reporting_to_id', $this->id)
                  ->orWhere('manager_id', $this->id);
        })
        ->whereNotIn('id', $visited)
        ->pluck('id')
        ->toArray();

        $allSubordinates = $subordinates;
        foreach ($subordinates as $subId) {
            $subUser = User::find($subId);
            if ($subUser) {
                $allSubordinates = array_merge($allSubordinates, $subUser->getSubordinateIds(array_merge($visited, $allSubordinates)));
            }
        }

        return array_unique($allSubordinates);
    }

    /**
     * Get list of user IDs that this user is allowed to view.
     */
    public function getAllowedUserIds()
    {
        if ($this->user_type === 'admin') {
            return User::pluck('id')->toArray();
        }

        return array_merge([$this->id], $this->getSubordinateIds());
    }

}
