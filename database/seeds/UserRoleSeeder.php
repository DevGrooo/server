<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user_id = $this->_getUserIdByUsername('admin');
        $role_codes = [
            
        ];
        $this->_setRoles($user_id, $role_codes);
    }

    /**
     * @param integer $user_id
     * @param array $roles
     */
    private function _setRoles($user_id, $role_codes)
    {
        $data = array();
        $roles = DB::table('roles')->get();
        if ($roles) {
            foreach ($roles as $role) {
                $data[] = [
                    'user_id' => $user_id,
                    'role_id' => $role->id,
                    'created_by' => 0,
                ];                
            }
            DB::table('user_role')->insert($data);
        }
    }

    /**
     * @param string user_group_code
     * @return integer
     */
    private function _getUserIdByUsername($username)
    {
        $user = DB::table('users')->where('username', $username)->first();
        if ($user) {
            return $user->id;
        }
        return 0;
    }
}
