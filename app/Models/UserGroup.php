<?php

namespace App\Models;

class UserGroup extends Model
{
    const GROUP_ADMIN = 'ADMIN';
    const GROUP_FUND_COMPANY  = 'FUND_COMPANY';
    const GROUP_FUND_DISTRIBUTOR  = 'FUND_DISTRIBUTOR';
    const GROUP_FUND_DISTRIBUTOR_STAFF  = 'FUND_DISTRIBUTOR_STAFF';
    const GROUP_INVESTOR  = 'INVESTOR';    

    const STATUS_ACTIVE = 1;
    const STATUS_LOCK = 2;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_groups';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_id', 'name', 'code', 'status', 'created_by', 'update_by'
    ];

    /**
     * Get the user groups.
     */
    public function users()
    {
        return $this->hasMany('App\Models\User', 'user_group_id');
    }

    public function roles()
    {
        return $this->belongsToMany('App\Models\Role', 'user_group_role', 'user_group_id', 'role_id')
                    ->select(['roles.id', 'roles.name', 'roles.code', 'roles.role_group_id', 'roles.parent_id'])
                    ->orderBy('parent_id', 'ASC')->orderBy('position', 'ASC');
    }

    public function getRoleGroups() 
    {
        $result = [];
        $role_groups = RoleGroup::where('status', RoleGroup::STATUS_ACTIVE)->orderBy('position', 'ASC')->get();
        if ($role_groups) {
            $rows = [];
            foreach ($role_groups as $role_group) {
                $rows[$role_group->id] = array();
                $rows[$role_group->id]['id'] = $role_group->id;
                $rows[$role_group->id]['name'] = $role_group->name;
                $rows[$role_group->id]['roles'] = null;
            }
            foreach ($this->roles as $role) {
                if ($role->role_group_id != 0) {
                    if ($role->parent_id == 0) {
                        $rows[$role->role_group_id]['roles'][$role->id] = array(
                            'id' => $role->id,
                            'name' => $role->name,
                            'code' => $role->code,
                            'sub_roles' => null
                        );
                    } else {
                        $rows[$role->role_group_id]['roles'][$role->parent_id]['sub_roles'][$role->id] = array(
                            'id' => $role->id,
                            'name' => $role->name,
                            'code' => $role->code,
                        );
                    }
                }
            }
            $result = $rows;
        }
        return $result;
    }

}
