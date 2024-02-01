<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FedCap extends Model
{
    public function caps()
    {
        return $this->hasMany(Cap::class, 'fed_cap_guid', 'guid');
    }
}
