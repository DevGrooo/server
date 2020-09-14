<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserGroupRole extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_group_role';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_group_id', 'role_id', 'created_by'
    ];
}
