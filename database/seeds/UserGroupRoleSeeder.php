<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserGroupRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user_group_id = 1; // admin
        $role_codes = [
            
        ];
        $this->_setRoles($user_group_id, $role_codes);
    }

    /**
     * @param integer $user_id
     * @param array $roles
     */
    private function _setRoles($user_group_id, $role_codes)
    {
        $data = array();
        $roles = DB::table('roles')->get();
        if ($roles) {
            foreach ($roles as $role) {
                $data[] = [
                    'user_group_id' => $user_group_id,
                    'role_id' => $role->id,
                    'created_by' => 0,
                ];                
            }
            DB::table('user_group_role')->insert($data);
        }
    }
}
