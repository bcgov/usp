<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attestation extends Model
{
    public function institution()
    {
        return $this->belongsTo(Institution::class, 'institution_guid', 'guid');
    }

    public function cap()
    {
        return $this->belongsTo(Cap::class, 'cap_guid', 'guid');
    }
}
