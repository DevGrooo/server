<?php

namespace App\Services\Transactions;

use App\Events\ResetPasswordUserEvent;
use App\Jobs\SendMailResetPasswordJob;
use App\Models\Api;
use App\Models\Role;
use App\Models\RoleApi;
use App\Models\User;
use App\Models\UserApi;
use App\Models\UserGroup;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;

class UserTransaction extends Transaction
{
    /**
     * @param array $params [
     * user_id,
     * role_ids,
     * created_by,
     * ]
     * @param boolean $allow_commit
     * @return array
     * @throws \Exception
     */
    public function updateRole($params, $allow_commit = false)
    {
        $response = null;
        // start transaction
        $this->beginTransaction($allow_commit);
        try {
            // check user exists
            $user = User::find($params['user_id']);
            if ($user) {
                // delete user_role old
                $role_rows = UserRole::where('user_id', $params['user_id']);
                if (!$role_rows->count() || $role_rows->delete()) {
                    // delete user_api
                    $api_rows = UserApi::where('user_id', $params['user_id']);
                    if ($api_rows->count() && !$api_rows->delete()) {
                        $this->error('Có lỗi khi cập nhật quyền API cho tài khoản');
                    }
                } else {
                    $this->error('Có lỗi khi cập nhật quyền tài khoản');
                }
                // check role_ids exists
                $roles = Role::whereIn('id', $params['role_ids'])->get();
                if ($roles) {
                    // add user_role
                    foreach ($roles as $role) {
                        $user_role = new UserRole();
                        $user_role->user_id = $params['user_id'];
                        $user_role->role_id = $role->id;
                        $user_role->created_by = $params['created_by'];
                        if (!$user_role->save()) {
                            $this->error('Có lỗi khi cập nhật quyền tài khoản');
                        }
                    }
                    $role_apis = RoleApi::select('api_id')->distinct()->whereIn('role_id', $params['role_ids'])->get();
                    if ($role_apis) {
                        // add user_api
                        foreach ($role_apis as $role_api) {
                            $user_api = new UserApi();
                            $user_api->user_id = $params['user_id'];
                            $user_api->api_id = $role_api->api_id;
                            if (!$user_api->save()) {
                                $this->error('Có lỗi khi cập nhật quyền API cho tài khoản');
                            }
                        }
                    }
                }
            } else {
                $this->error('Người dùng không tồn tại');
            }
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }

    /**
     * @param array $params
     * @param boolean $allow_commit
     * @return array
     * @throws \Exception
     */
    public function changePassword($params, $allow_commit = false)
    {

        $response = null;
        $this->beginTransaction($allow_commit);
        try {
            // check user exists
            $user = User::find($params['user_id']);
            if ($user) {
                // check password true
                $password = $user->password;
                if ($user->validatePassword($params['current_password']) == $password) {
                    //Change password
                    $new_password = $params['new_password'];
                    $user->password = $new_password;
                    $user->updated_by = $params['updated_by'];
                    if (!$user->save()) {
                        $this->error('Có lỗi khi đổi mật khẩu');
                    }
                } else {
                    $this->error('Mật khẩu cũ không đúng');
                }
            } else {
                $this->error('Người dùng không tồn tại');
            }
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }

    /**
     * @param array $params
     * @param boolean $allow_commit
     * @return array
     * @throws \Exception
     */
    public function resetPassword($params, $allow_commit = false)
    {

        $response = null;
        $this->beginTransaction($allow_commit);
        try {
            // check user exists
            $user = User::find($params['user_id']);
            if ($user) {
                //reset password
                $new_password = $user->getRandomPassword();
                $user->password = $new_password;
                $user->updated_by = $params['updated_by'];
                if (!$user->save()) {
                    $this->error('Có lỗi khi đổi mật khẩu');
                } else {                       
                    // send mail
                    // event(new ResetPasswordUserEvent($user, $new_password));
                    $job = (new SendMailResetPasswordJob($user, $new_password))->onConnection(config('queue.default'))->onQueue('notifications');
                    dispatch($job);
                }
            } else {
                $this->error('Người dùng không tồn tại');
            }
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }

    /**
     * @param array $params
     * @param boolean $allow_commit
     * @return array
     * @throws \Exception
     */
    public function create($params, $allow_commit = false)
    {
        $response = null;
        $this->beginTransaction($allow_commit);
        try {
            // Check email exist and username exist
            $email_exist = User::where('email', '=', $params['email'])->first();
            $username_exist = User::where('username', '=', $params['username'])->first();
            // Create string
            $char_pass = '0123456789abcdefghijklmnopqrstuvwxyz';
            //Check user_group_id in user group
            $user_group = UserGroup::where('id', $params['user_group_id'])->first();
            // Check user originator
            $user = Auth::user()->id;
            if ($email_exist === null && $username_exist === null) {
                if ($user_group) {
                    $new_user = new User();
                    $new_user->user_group_id = $params['user_group_id'];
                    $new_user->ref_id = $params['ref_id'];
                    $new_user->username = $params['username'];
                    $new_user->fullname = $params['fullname'];
                    $new_user->password = md5(substr(str_shuffle($char_pass), 0, 6));
                    $new_user->email = $params['email'];
                    $new_user->mobile = $params['mobile'];
                    $new_user->status = $params['status'];
                    $new_user->updated_by = $user;
                    $new_user->created_by = $user;
                    if ($user_group->id == 1) {
                        $new_user->ref_type = '';
                    } else {
                        $new_user->ref_type = $user_group->code;
                    }
                    $new_user->save();
                } else {
                    $this->error('User group này không có ');
                }
            } else if ($email_exist != null) {
                $this->error('Email này đã tồn tại');
            } else if ($username_exist != null) {
                $this->error('User name này đã tồn tại');
            }
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }

    /**
     * @param integer $params[
     * user_id,
     * fullname,
     * email,
     * mobile,
     * updated_by
     * ]
     * @throws \Exception
     * @return array
     */
    public function update($params, $allow_commit = false)
    {
        $response = null;
        $this->beginTransaction($allow_commit);
        try {
        //Check user id exist
            $user = User::where('id', $params['user_id'])->first();
            if( $user){
                $other_user = User::select('id','email')->whereNotIn('id',[$params['user_id']])->where('email', $params['email'])->first();
                if ($other_user) {
                    $this->error('Email đã tồn tại');
                }
                $user->fullname =  $params['fullname'];
                $user->email = $params['email'];
                $user->mobile = $params['mobile'];
                $user->updated_by = $params['updated_by'];
                if (!$user->save()) {
                    $this->error('Có lỗi khi cập nhật thông tin người dùng');
                }
            }else {
                $this->error('Người dùng không tồn tại');
            }
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }

    /**
     * @param integer $user_id
     * @param string $password
     * @throws \Exception
     * @return array
     */
    public function updatePassword($params, $allow_commit = false){

        $response = null;
        $this->beginTransaction($allow_commit);
        try {
            $user = User::find($params['user_id']);
            $new_password=$params['new_password'];
            $user->password= $new_password;
            $user->save();
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }

    /**
     * @param array $params
     * @param boolean $allow_commit
     * @return array
     * @throws \Exception
     */
    public function lock($params, $allow_commit = false){
        $response = null;
        $this->beginTransaction($allow_commit);
        try {
            //Check user id exist
            $user = User::where('id', '=', $params['user_id'])->first();
            if ($user) {
                if ($user->isActive()) { //Lock user
                    $user->status = User::STATUS_LOCK;
                    $user->updated_by = $params['updated_by'];
                    $user->save();
                } else {
                    $this->error('Người dùng không hợp lệ');
                }
            } else {
                $this->error('Người dùng không tồn tại');
            }
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }

    /**
     * @param array $params
     * @param boolean $allow_commit
     * @return array
     * @throws \Exception
     */
    public function active($params, $allow_commit = false){
        $response = null;
        $this->beginTransaction($allow_commit);
        try {
            //Check user id exist
            $user = User::where('id', '=', $params['user_id'])->first();
            if ($user) {
                if ($user->status == User::STATUS_LOCK) { //Lock user
                    $user->status = User::STATUS_ACTIVE;
                    $user->updated_by = $params['updated_by'];
                    $user->save();
                } else {
                    $this->error('Người dùng không hợp lệ');
                }
            } else {
                $this->error('Người dùng không tồn tại');
            }
        } catch (\Exception $e) {
            // rollback transaction
            $this->rollback($allow_commit, $e);
        }
        // commit and response
        return $this->response($allow_commit, $response);
    }
}
