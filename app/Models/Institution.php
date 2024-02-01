<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    public function attestations()
    {
        return $this->hasMany(Attestation::class, 'institution_guid', 'guid');
    }
    public function staff()
    {
        return $this->hasMany(InstitutionStaff::class, 'institution_guid', 'guid');
    }
    public function users()
    {
        return $this->hasManyThrough(User::class, InstitutionStaff::class,
            'institution_guid', 'guid', 'guid', 'user_guid');
    }

}
