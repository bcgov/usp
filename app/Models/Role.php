<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name'];

    public const SUPER_ADMIN = 'Super Admin';

    public const Ministry_ADMIN = 'Ministry Admin';
    public const Institution_ADMIN = 'Institution Admin';

    public const Ministry_USER = 'Ministry User';
    public const Institution_USER = 'Institution User';

    public const Ministry_GUEST = 'Ministry Guest';
    public const Institution_GUEST = 'Institution Guest';


    /**
     * The roles that belong to the user.
     */
    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'role_user');
    }
}
