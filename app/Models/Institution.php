<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['guid', 'dli', 'name', 'legal_name', 'address1', 'address2', 'primary_contact',
'primary_email', 'city', 'postal_code', 'province', 'public', 'active_status', 'standing_status',
'api_id', 'api_key', 'last_touch_by_user_guid',];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['last_touch_by_user_guid', 'api_key', 'api_id'];

    public function attestations()
    {
        return $this->hasMany(Attestation::class, 'institution_guid', 'guid');
    }
    public function staff()
    {
        return $this->hasMany(InstitutionStaff::class, 'institution_guid', 'guid');
    }
    public function users()
    {
        return $this->hasManyThrough(User::class, InstitutionStaff::class,
            'institution_guid', 'guid', 'guid', 'user_guid');
    }

}
