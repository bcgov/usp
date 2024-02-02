<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstitutionStaff extends Model
{
    protected $appends = ['is_admin'];

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
