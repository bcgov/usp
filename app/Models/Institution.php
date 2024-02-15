<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Institution extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['guid', 'dli', 'name', 'legal_name', 'address1', 'address2', 'primary_contact',
        'primary_email', 'city', 'postal_code', 'province', 'public', 'active_status', 'standing_status',
        'api_id', 'api_key', 'bceid_business_guid', 'last_touch_by_user_guid', ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['last_touch_by_user_guid', 'api_key', 'api_id'];

    public function attestations()
    {
        return $this->hasMany(Attestation::class, 'institution_guid', 'guid')->orderBy('created_at', 'asc');
    }

    public function programs()
    {
        return $this->hasMany(Program::class, 'institution_guid', 'guid');
    }

    public function caps()
    {
        return $this->hasMany(Cap::class, 'institution_guid', 'guid');
    }

    public function activeCaps()
    {
        return $this->hasMany(Cap::class, 'institution_guid', 'guid')->active();
    }

    public function activeInstCaps()
    {
        return $this->activeCaps()->onlyInstCaps();
    }

    public function activeProgramCaps()
    {
        return $this->activeCaps()->onlyProgCaps()->with('program');
    }

    public function staff()
    {
        return $this->hasMany(InstitutionStaff::class, 'institution_guid', 'guid')->whereHas('user');
    }

    public function users()
    {
        return $this->hasManyThrough(User::class, InstitutionStaff::class,
            'institution_guid', 'guid', 'guid', 'user_guid');
    }

    /**
     * Scope a query to only include admin users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('active_status', true);
    }
}
