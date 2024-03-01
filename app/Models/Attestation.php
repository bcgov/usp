<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attestation extends Model
{
    use SoftDeletes;

    protected $appends = ['issued_by_name'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['guid', 'fed_guid', 'cap_guid', 'fed_cap_guid', 'institution_guid', 'program_guid', 'first_name', 'last_name', 'id_number',
        'dob', 'status', 'expiry_date', 'last_touch_by_user_guid', 'created_by_user_guid', 'issued_by_user_guid', 'student_number',
        'address1', 'address2', 'email', 'city', 'zip_code', 'province', 'country', 'gt_fifty_pct_in_person', 'issue_date', 'program_name', ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['last_touch_by_user_guid', 'created_by_user_guid'];

    public function institution()
    {
        return $this->belongsTo(Institution::class, 'institution_guid', 'guid');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_guid', 'guid');
    }

    public function cap()
    {
        return $this->belongsTo(Cap::class, 'cap_guid', 'guid');
    }

    public function fedCap()
    {
        return $this->belongsTo(FedCap::class, 'fed_cap_guid', 'guid');
    }

    /**
     * Scope a query to only include admin users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIssued($query)
    {
        return $query->where('status', 'Issued');
    }

    public function getIssuedByNameAttribute()
    {
        if (is_null($this->issued_by_user_guid)) {
            return null;
        }

        $user = User::where('guid', $this->issued_by_user_guid)->first();

        return $user->first_name.' '.$user->last_name;
    }
}
