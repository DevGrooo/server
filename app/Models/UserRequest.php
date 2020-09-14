<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRequest extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_requests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'opt', 'checksum', 'number_fail', 'expired_at'
    ];

    protected $dates = [
        'expired_at',
    ];


    /**
     * Get the user that owns the user request.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }

    /**
     * @param integer $start timestamp
     */
    public function getExpiredAt($start)
    {
        return $start + config('user_request.expired_second');
    }

}
