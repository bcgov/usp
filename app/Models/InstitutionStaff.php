<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstitutionStaff extends Model
{
    public function institution()
    {
        return $this->belongsTo(Institution::class, 'institution_guid', 'guid');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_guid', 'guid');
    }
}
