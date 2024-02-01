<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cap extends Model
{
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
