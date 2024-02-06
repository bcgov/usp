<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InstitutionStaff extends Model
{
    protected $appends = ['is_admin'];
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['guid', 'user_guid', 'institution_guid', 'bceid_business_guid', 'bceid_user_guid', 'bceid_user_id',
        'bceid_user_name', 'bceid_user_email', 'status', 'last_touch_by_user_guid',];
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
        foreach ($this->user->roles as $role) {
            if($role->name === Role::Institution_ADMIN){
                $admin = true;
                break;
            }
        }
        return $admin;
    }
}
