<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    const STATUS_ACTIVE = 1;
    const STATUS_LOCK = 2;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role_group_id', 'name', 'code', 'description', 'parent_id', 'position', 'left_index', 'right_index', 'status', 'update_at', 'update_by'
    ];

    /**
     * Get the roles .
     */
    public function role_group()
    {
        return $this->belongsTo('App\Models\RoleGroup', 'role_group_id');
    }

    public function user_group_roles()
    {
        return $this->belongsToMany('App\Models\UserGroup', 'user_group_role', 'user_group_id', 'role_id');
    }

    public function user_roles()
    {
        return $this->belongsToMany('App\Models\User', 'user_role', 'user_id', 'role_id');
    }

    /**
     * Get the role api for the role.
     */
    public function role_api()
    {
        return $this->hasMany('App\Models\RoleApi', 'role_api');
    }

    /**
     * The api that belong to the role.
     */
    public function api()
    {
        return $this->belongsToMany('App\Models\Api', 'role_api', 'role_id', 'api_id');
    }
}
