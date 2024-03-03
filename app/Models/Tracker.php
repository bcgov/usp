<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tracker extends Model
{
    protected $fillable = ['user_guid', 'user_name', 'action', 'model_name', 'model_id', 'model_data'];
}
