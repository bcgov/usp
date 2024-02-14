<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttestationPdf extends Model
{
    protected $fillable = ['guid', 'attestation_guid', 'content', ];
}
