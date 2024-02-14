<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Util extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['field_name', 'field_type', 'field_description', 'active_flag'];

    public static function getSortedUtils()
    {
        $utils = Util::select('id', 'field_name', 'field_type')->where('active_flag', true)->get();

        $cat_utils = [];
        $cat_titles = [];
        foreach ($utils as $util) {
            $cat_utils[$util->field_type][] = $util;
        }
        foreach ($cat_utils as $k => $v) {
            $cat_titles[] = $k;
        }
        sort($cat_titles);

        return $cat_utils;
    }
}
