<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FedCap extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['guid', 'start_date', 'end_date', 'total_attestations', 'status', 'comment',
        'last_touch_by_user_guid',];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['last_touch_by_user_guid'];

    public function caps()
    {
        return $this->hasMany(Cap::class, 'fed_cap_guid', 'guid');
    }
}
