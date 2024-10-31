<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FedCap extends Model {

    use SoftDeletes;

    protected $appends = ['remaining_cap', 'remaining_reserved_graduate_cap'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'guid',
        'start_date',
        'end_date',
        'total_attestations',
        'total_reserved_graduate_attestations',
        'status',
        'comment',
        'last_touch_by_user_guid',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['last_touch_by_user_guid'];

    public function caps() {
        return $this->hasMany(Cap::class, 'fed_cap_guid', 'guid')->active();
    }

    public function institutionCaps() {
        return $this->hasMany(Cap::class, 'fed_cap_guid', 'guid')
            ->select('caps.*', 'i.id as inst_id', 'i.name as inst_name')
            ->where('program_guid', NULL)
            ->join('institutions as i', 'i.guid', '=', 'caps.institution_guid')
            ->orderBy('i.name')
            ->where('caps.active_status', TRUE);
    }

    public function getRemainingCapAttribute() {
        $total = $this->caps->sum('total_attestations');

        return $this->total_attestations - $total;
    }

    public function getRemainingReservedGraduateCapAttribute() {
        $total = $this->caps->sum('total_reserved_graduate_attestations');

        return $this->total_reserved_graduate_attestations - $total;
    }

    /**
     * Scope a query to only include admin users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query) {
        return $query->where('status', 'Active');
    }

}
