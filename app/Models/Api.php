<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Api extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'api';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'code', 'method', 'route', 'description',
        'status', 'created_by', 'updated_by'
    ];

    /**
     * Get the user api for the api.
     */
    public function user_api()
    {
        return $this->hasMany('App\Models\UserApi', 'api_id');
    }

    /**
     * The users that belong to the api.
     */
    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'user_api', 'api_id', 'user_id');
    }

    /**
     * Get the role api for the api.
     */
    public function role_api()
    {
        return $this->hasMany('App\Models\RoleApi', 'api_id');
    }

    /**
     * The roles that belong to the api.
     */
    public function roles()
    {
        return $this->belongsToMany('App\Models\Role', 'role_api', 'api_id', 'role_id');
    }
}
