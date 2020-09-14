<?php

require_once('MySeeder.php');

class ApiSeeder extends MySeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->_makeApiUser();
    }

    /**
     * Add apis base for role
     */
    private function _makeApiUser()
    {
        $query = "
        INSERT INTO api(name,code,method,router,description,status,created_by,updated_by) VALUES('User login','UserController@login','put','user/login','Đăng nhập hệ thống', 1, 1, 1);
        INSERT INTO api(name,code,method,router,description,status,created_by,updated_by) VALUES('User request reset password','UserController@requestResetPassword','put','user/request_reset_password','Gửi yêu cầu đặt lại mật khẩu', 1, 1, 1);
        INSERT INTO api(name,code,method,router,description,status,created_by,updated_by) VALUES('User verify checksum reset password','UserController@verifyChecksumResetPassword','put','user/verify_checksum_reset_password','Xác thực URL yêu cầu đặt lại mật khẩu', 1, 1, 1);
        INSERT INTO api(name,code,method,router,description,status,created_by,updated_by) VALUES('User verify otp reset password','UserController@verifyOtpResetPassword','put','user/verify_otp_reset_password','Xác thực OTP yêu cầu đặt lại mật khẩu', 1, 1, 1);
        INSERT INTO api(name,code,method,router,description,status,created_by,updated_by) VALUES('User reset password','UserController@resetPassword','put','user/reset_password','Đổi mật khẩu khi đã đăng nhập', 1, 1, 1);
        INSERT INTO api(name,code,method,router,description,status,created_by,updated_by) VALUES('User detail','UserController@detail','get','user/detail/{user_id}','Xem chi tiết thông tin người dùng', 1, 1, 1);
        INSERT INTO api(name,code,method,router,description,status,created_by,updated_by) VALUES('User get list','UserController@getList','get','user/get_list','Lấy danh sách người dùng', 1, 1, 1);
        INSERT INTO api(name,code,method,router,description,status,created_by,updated_by) VALUES('User create','UserController@create','put','user/create','Tạo tài khoản đăng nhập cho người dùng', 1, 1, 1);
        INSERT INTO api(name,code,method,router,description,status,created_by,updated_by) VALUES('User update','UserController@update','put','user/update','Cập nhật thông tin người dùng', 1, 1, 1);
        INSERT INTO api(name,code,method,router,description,status,created_by,updated_by) VALUES('User lock','UserController@lock','get','user/lock/{user_id}','Khóa người dùng', 1, 1, 1);
        INSERT INTO api(name,code,method,router,description,status,created_by,updated_by) VALUES('User active','UserController@active','get','user/active/{user_id}','Mở khóa người dùng', 1, 1, 1);
        INSERT INTO api(name,code,method,router,description,status,created_by,updated_by) VALUES('User update role','UserController@updateRole','put','user/update_role','Cập nhật quyền cho người dùng', 1, 1, 1);
        INSERT INTO api(name,code,method,router,description,status,created_by,updated_by) VALUES('User get ref list','UserController@getRefList','get','user/get_ref_list/{user_group_id}','Lấy danh sách tham chiếu đến người dùng(NĐT,ĐLPP,CTQLQ)', 1, 1, 1);
        INSERT INTO api(name,code,method,router,description,status,created_by,updated_by) VALUES('User get user group list for update','UserController@getUserGroupList','put','user/get_user_group_list','Lấy danh sách nhóm người dùng', 1, 1, 1);
        INSERT INTO api(name,code,method,router,description,status,created_by,updated_by) VALUES('User get status','UserController@getStatus','get','user/get_status','Lấy danh sách trạng thái người dùng', 1, 1, 1);
        INSERT INTO api(name,code,method,router,description,status,created_by,updated_by) VALUES('User group get role list','UserGroupController@getRoleList','get','user_group/get_role_list/{user_group_id}','Lấy danh sách quyền theo nhóm người dùng', 1, 1, 1);
        INSERT INTO api(name,code,method,router,description,status,created_by,updated_by) VALUES('User group get list','UserGroupController@getList','put','user_group/get_list','Lấy danh sách nhóm người dùng', 1, 1, 1);
        INSERT INTO api(name,code,method,router,description,status,created_by,updated_by) VALUES('User group create','UserGroupController@create','put','user_group/create','Tạo nhóm người dùng', 1, 1, 1);
        INSERT INTO api(name,code,method,router,description,status,created_by,updated_by) VALUES('User group update','UserGroupController@update','put','user_group/update','Cập nhật nhóm người dùng', 1, 1, 1);
        INSERT INTO api(name,code,method,router,description,status,created_by,updated_by) VALUES('User group lock','UserGroupController@lock','get','user_group/lock/{user_group_id}','Khóa nhóm người dùng', 1, 1, 1);
        INSERT INTO api(name,code,method,router,description,status,created_by,updated_by) VALUES('User group active','UserGroupController@active','get','user_group/active/{user_group_id}','Mở khóa nhóm người dùng', 1, 1, 1);
        ";
        $this->_executeMultiRaw($query);
    }
}
