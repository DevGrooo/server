<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleGroup extends Model
{
    const STATUS_ACTIVE = 1;
    const STATUS_LOCK = 2;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'role_groups';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'data', 'position', 'status', 'update_at', 'update_by',
    ];

    /**
     * Get the role groups .
     */
    public function roles()
    {
        return $this->hasMany('App\Models\Roles', 'role_group_id');
    }
}
