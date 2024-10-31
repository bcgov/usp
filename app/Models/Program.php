<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Program extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['guid', 'institution_guid', 'cap_guid', 'program_guid', 'program_name', 'program_type', 'credential',
        'total_duration_hrs', 'total_duration_weeks', 'tuition_domestic', 'tuition_international', 'work_experience_required',
        'delivery_in_class', 'delivery_distance', 'delivery_combined', 'noc_code', 'cip_code', 'active_status', 'restrictions',
        'status', 'last_touch_by_user_guid', 'program_graduate' ];

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

    public function cap()
    {
        return $this->hasOne(Cap::class, 'program_guid', 'guid')->active();
    }
}
