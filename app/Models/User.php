<?php

namespace App\Models;

class User extends Model
{
    const STATUS_ACTIVE = 1;
    const STATUS_LOCK = 2;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_group_id', 'ref_id', 'ref_type', 'username', 'password', 'fullname', 'email', 'mobie', 'status', 'create_by', 'update_by'
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = $this->_encrypt($value);
    }

    /**
     * Get the users  for the user groups.
     */
    public function user_group()
    {        
        return $this->belongsTo('App\Models\UserGroup', 'user_group_id');
    }

    public function roles()
    {
        return $this->belongsToMany('App\Models\Role', 'user_role', 'user_id', 'role_id')
                    ->select(['roles.id', 'roles.name', 'roles.code', 'roles.parent_id', 'roles.publish', 'roles.role_group_id'])->orderBy('position', 'ASC');
    }

    /**
     * Get the user tokens for the user.
     */
    public function user_tokens()
    {
        return $this->hasMany('App\Models\UserToken', 'user_id');
    }

    /**
     * Get the user requests for the user.
     */
    public function user_requests()
    {
        return $this->hasMany('App\Models\UserRequest', 'user_id');
    }

    /**
     * Get the user api for the user.
     */
    public function user_api()
    {
        return $this->hasMany('App\Models\UserApi', 'user_id');
    }

    /**
     * The api that belong to the user.
     */
    public function api()
    {
        return $this->belongsToMany('App\Models\Api','user_api', 'user_id', 'api_id');

    }

    public function ref()
    {
        return $this->morphTo();
    }

    public static function getListStatus() {
        return array(
            User::STATUS_ACTIVE => trans('status.user.active'),
            User::STATUS_LOCK => trans('status.user.lock'),
        );
    }

    /**
     * Get list Api.code
     * @return array
     */
    public function getApiCodes()
    {
        $api_codes = array();
        if ($this->api()->exists()) {
            foreach ($this->api->all() as $api) {
                $api_codes[$api->code] = $api->code;
            }
        }
        return $api_codes;
    }

    /**
     * Return true if user active
     * @return boolean
     */
    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * Check password current user
     * @return boolean
     */
    public function validatePassword($password)
    {
        return $this->_encrypt($password) === $this->password;
    }

    /**
     * Function encrypt password
     */
    private function _encrypt($value)
    {
        return md5($value);
    }

    public function getUserGroupCode()
    {
        if ($this->user_group) {
            return $this->user_group->code;
        }
        return '';
    }

    public function getRoleGroups()
    {
        $result = array();
        $groups = array();
        foreach ($this->roles as $role) {
            if ($role->role_group_id != 0) {
                $role_group = $role->role_group;
                if (!array_key_exists($role_group->id, $groups)) {
                    $groups[$role_group->id] = array();
                    $groups[$role_group->id]['name'] = trans($role_group->name);
                    $groups[$role_group->id]['data'] = $role_group->data;
                    $groups[$role_group->id]['position'] = $role_group->position;
                    $groups[$role_group->id]['roles'] = array();
                }
                $role->name = trans($role->name);
                $groups[$role_group->id]['roles'][] = $role;
            }
        }
        foreach ($groups as $group) {
            $result[] = $group;
        }
        for ($i = 0; $i < count($result) - 1; $i++) {
            for ($j = $i + 1; $j < count($result); $j++) {
                if ($result[$i]['position'] > $result[$j]['position']) {
                    $temp = $result[$i];
                    $result[$i] = $result[$j];
                    $result[$j] = $temp;
                }
            }
        }
        return $result;
    }

    public function getSelfRoles()
    {
        $result = array();
        foreach ($this->roles as $role) {
            if ($role->role_group_id == 0) {
                $role->name = trans($role->name);
                $result[] = $role;
            }
        }        
        return $result;
    }

    public function getAllRoles()
    {
        $result = array();
        foreach ($this->roles as $role) {
            $role->name = trans($role->name);
            $result[] = $role;
        }        
        return $result;
    }

    public function getRandomPassword($length = 6) {
        return substr(md5(rand(1,1000000)),0, $length);
    }
}
