<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserApiSeeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user_id = $this->_getUserIdByUsername('admin');
        $this->_setApis($user_id);
    }

    /**
     * @param integer $user_id
     * @param array $roles
     */
    private function _setApis($user_id)
    {
        DB::insert(DB::raw('INSERT INTO user_api(user_id, api_id) 
            SELECT user_role.user_id, role_api.api_id FROM role_api, user_role 
            WHERE role_api.role_id = user_role.role_id AND user_role.user_id = :user_id'), ['user_id' => $user_id]);
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
