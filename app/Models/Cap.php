<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cap extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['guid', 'fed_cap_guid', 'institution_guid', 'start_date', 'end_date', 'total_attestations', 'status', 'comment',
        'last_touch_by_user_guid',];
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
}
