<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FedCap extends Model
{
    use SoftDeletes;

    protected $appends = ['remaining_cap'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['guid', 'start_date', 'end_date', 'total_attestations', 'status', 'comment',
        'last_touch_by_user_guid', ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['last_touch_by_user_guid'];

    public function caps()
    {
        return $this->hasMany(Cap::class, 'fed_cap_guid', 'guid')->active();
    }

    public function institutionCaps()
    {
        return $this->hasMany(Cap::class, 'fed_cap_guid', 'guid')
            ->select('caps.*', 'i.id as inst_id', 'i.name as inst_name')
            ->where('program_guid', null)
            ->join('institutions as i', 'i.guid', '=', 'caps.institution_guid')
            ->orderBy('i.name')
            ->where('caps.active_status', true);
    }

    public function getRemainingCapAttribute()
    {
        $total = 0;
        foreach ($this->caps as $cap) {
            $total += $cap->total_attestations;
        }

        if ($total < 0) {
            return $this->total_attestations + $total;
        }

        return $this->total_attestations - $total;
    }

    /**
     * Scope a query to only include admin users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }
}
