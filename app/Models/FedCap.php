<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FedCap extends Model {

    use SoftDeletes;

    protected $appends = ['remaining_cap', 'remaining_undergraduate_cap', 'total_attestations_with_over_allocation'];

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
        'over_allocation_percentage',
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

    /**
     * Per-instance memoization for caps column sums (see sumCapsColumn()).
     *
     * @var array<string, int>
     */
    protected $capsSumCache = [];

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
        return $this->total_attestations - $this->sumCapsColumn('total_attestations');
    }

    public function getRemainingUndergraduateCapAttribute() {
        return $this->sumCapsColumn('total_attestations') - $this->sumCapsColumn('total_reserved_graduate_attestations');
    }

    /**
     * Sum a column across this fed cap's active caps.
     *
     * Uses the already-loaded relation when present; otherwise aggregates in SQL
     * without hydrating (and therefore without serializing) the caps. This avoids
     * an N+1 where serializing the lazily-loaded caps would trigger the Cap
     * model's per-cap COUNT accessors. Results are memoized per instance.
     */
    private function sumCapsColumn(string $column): int {
        if (! array_key_exists($column, $this->capsSumCache)) {
            $this->capsSumCache[$column] = $this->relationLoaded('caps')
                ? (int) $this->caps->sum($column)
                : (int) $this->caps()->sum($column);
        }

        return $this->capsSumCache[$column];
    }

    public function getTotalAttestationsWithOverallocationAttribute() {
        return (int) floor($this->total_attestations * (1 + $this->over_allocation_percentage));
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
