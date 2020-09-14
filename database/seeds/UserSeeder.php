<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user_id = DB::table('users')->insertGetId([
            'user_group_id' => $this->_getUserGroupIdByCode('admin'),
            'ref_id' => 0,
            'ref_type' => '',
            'username' => 'admin',
            'password' => 'e10adc3949ba59abbe56e057f20f883e',
            'fullname' => 'Admin',
            'email' => 'phuonglh@hiworld.com.vn',
            'mobile' => '0987654321',
            'status' => 1,
            'created_by' => 0,
            'updated_by' => 0,
        ]);
    }

    /**
     * @param string user_group_code
     * @return integer
     */
    private function _getUserGroupIdByCode($user_group_code)
    {
        $user_group = DB::table('user_groups')->where('code', strtoupper($user_group_code))->first();
        if ($user_group) {
            return $user_group->id;
        }
        return 0;
    }
}
