<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Auth;

class Cap extends Model
{
    use SoftDeletes;

    protected $appends = ['inst_active_cap_stat', 'inst_active_res_grad_cap_stat'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'guid',
        'fed_cap_guid',
        'institution_guid',
        'program_guid',
        'start_date',
        'end_date',
        'total_attestations',
        'total_reserved_graduate_attestations',
        'status',
        'comment',
        'external_comment',
        'last_touch_by_user_guid',
        'parent_cap_guid',
        'issued_attestations',
        'issued_reserved_graduate_attestations',
        'draft_attestations',
        'draft_reserved_graduate_attestations',
        'confirmed',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['last_touch_by_user_guid'];

    public function fedCap()
    {
        return $this->belongsTo(FedCap::class, 'fed_cap_guid', 'guid');
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class, 'institution_guid', 'guid');
    }

    public function attestations()
    {
        return $this->hasMany(Attestation::class, 'cap_guid', 'guid');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_guid', 'guid');
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

    public function scopeSelectedFedcap($query)
    {
        $guid = Cache::get('global_fed_caps_' . Auth::id());

        if (is_null($guid)) {
            return $query;
        }

        return $query->where('fed_cap_guid', $guid['default']);
    }

    public function scopeOnlyInstCaps($query)
    {
        return $query->active()->where('program_guid', null);
    }

    public function scopeOnlyProgCaps($query)
    {
        return $query->active()->where('program_guid', '!=', null);
    }

    public function getInstActiveCapStatAttribute()
    {
        $issuedInstAttestations = Attestation::whereIn('status', ['Issued', 'Declined'])
            ->where('institution_guid', $this->institution_guid)
            ->where('fed_cap_guid', $this->fed_cap_guid)
            ->count();

        return [
            'total' => $this->total_attestations,
            'issued' => $issuedInstAttestations,
            'remain' => $this->total_attestations - $issuedInstAttestations,
        ];
    }

    public function getInstActiveResGradCapStatAttribute()
    {
        $issuedInstResGradAttestations = Attestation::whereIn('status', ['Issued', 'Declined'])
            ->where('institution_guid', $this->institution_guid)
            ->where('fed_cap_guid', $this->fed_cap_guid)
            ->whereHas('program', function ($query) {
                $query->where('program_graduate', true);
            })
            ->count();

        return [
            'total_reserved_graduate' => $this->total_reserved_graduate_attestations,
            'issued_reserved_graduate' => $issuedInstResGradAttestations,
        ];
    }
}
