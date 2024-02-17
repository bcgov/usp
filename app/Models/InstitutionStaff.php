<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class InstitutionStaff extends Model
{
    use SoftDeletes;

    protected $appends = ['is_admin'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['guid', 'user_guid', 'institution_guid', 'bceid_business_guid', 'bceid_user_guid', 'bceid_user_id',
        'bceid_user_name', 'bceid_user_email', 'status', 'last_touch_by_user_guid', ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['last_touch_by_user_guid'];

    public function institution()
    {
        return $this->belongsTo(Institution::class, 'institution_guid', 'guid');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_guid', 'guid');
    }

    public function getIsAdminAttribute()
    {
        $admin = false;
        if(Auth::check()){
            foreach (Auth::user()->roles as $role) {
                if ($role->name === Role::Institution_ADMIN) {
                    $admin = true;
                    break;
                }
            }
        }

        return $admin;
    }
}
