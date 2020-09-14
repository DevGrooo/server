<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserApi extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_api';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'api_id'
    ];

    /**
     * Get the user that owns the user api.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }

    /**
     * Get the api that owns the user api.
     */
    public function api()
    {
        return $this->belongsTo('App\Models\Api','api_id');
    }
}
