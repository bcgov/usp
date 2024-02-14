<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attestation extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['guid', 'cap_guid', 'institution_guid', 'program_guid', 'first_name', 'last_name', 'id_number',
        'dob', 'status', 'expiry_date', 'last_touch_by_user_guid', 'created_by_user_guid',
        'address1', 'address2', 'email', 'city', 'zip_code', 'province', 'country', 'gt_fifty_pct_in_person', ];

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
}
