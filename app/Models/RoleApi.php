<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleApi extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'role_api';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
       'role_api', 'api_id'
    ];

    /**
     * Get the role that owns the role api.
     */
    public function role()
    {
        return $this->belongsTo('App\Models\Role','role_id');
    }

    /**
     * Get the api that owns the role api.
     */
    public function api()
    {
        return $this->belongsTo('App\Models\Api','api_id');
    }
}
