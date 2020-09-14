<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\TableTrait;
use App\Models\FundCompany;
use App\Models\FundDistributor;
use App\Models\FundDistributorStaff;
use App\Models\Investor;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\UserRequest;
use App\Models\UserToken;
use App\Services\Transactions\UserRequestTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\Transactions\UserTransaction;
use App\Services\Transactions\UserTokenTransaction;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;


/**
 * @group  UserApi
 */
class UserController extends Controller
{
    use TableTrait;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['login', 'requestResetPassword', 'verifyChecksumResetPassword', 'verifyOtpResetPassword'] ]);
    }

    /**
     * User login
     *
     * Đăng nhập hệ thống
     *
     * @header {String} token={{TOKEN}}
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam params.username string required Username login. Example: admin
     * @bodyParam params.password string required Password login. Example: 123456
     * @response {
     * "status": false,
     * "code"": 403,
     * "messages": "Access denied",
     * "response": null
     * }
     */
    public function login(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.username' => 'required|string|max:255',
            'params.password' => 'required|string|max:20'
        ]);
        $params = $inputs['params'];
        $user = User::where('username', $params['username'])->first();
        if ($user) {
            if ($user->validatePassword($params['password'])) {
                if ($user->isActive()) {
                    $user->roles;
                    if (!UserToken::isExistsTokenForUser($user->id, $token)) {
                        $result = (new UserTokenTransaction)->makeToken(['user_id' => $user->id], true);
                        $token = UserToken::encryptHashToken($result['response']['token']);
                    }                    
                    return $this->responseSuccess([
                        'token' => $token,
                        'user' => $user,
                    ]);
                } else {
                    $this->error('Tài khoản đăng nhập đang bị khóa', Response::HTTP_FORBIDDEN);
                }
            } else {
                $this->error('Tên truy cập hoặc mật khẩu không đúng', Response::HTTP_FORBIDDEN);
            }
        } else {
            $this->error('Tên truy cập hoặc mật khẩu không đúng', Response::HTTP_FORBIDDEN);
        }
    }

    /**
     * Profile
     *
     * Lấy thông tin tài khoản đã đăng nhập theo token
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam token string required User Token.
     * @response {
     * "status": false,
     * "code"": 403,
     * "messages": "Access denied",
     * "response": null
     * }
     */
    public function profile(Request $request)
    {
        $user = User::where('id', auth()->user()->id)->where('status', User::STATUS_ACTIVE)->first();
        if ($user) {
            return $this->responseSuccess([
                'user' => [
                    'id' => $user->id,
                    'user_group_name' => $user->user_group->name,
                    'username' => $user->username,
                    'fullname' => $user->fullname,
                    'email' => $user->email,
                    'mobile' => $user->mobile,
                    'self_roles' => $user->getSelfRoles(),
                    'all_roles' => $user->getAllRoles(),
                ],
                'role_groups' => $user->getRoleGroups()
            ]);
        } else {
            $this->error('Người dùng không tồn tại hoặc bị khóa', Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Profile update
     *
     * Cập nhật profile
     *
     * @bodyParam params.fullname string required. Example: le huy phuong
     * @bodyParam params.email string required. Example: phuonglh@hiworl.com.vn
     * @bodyParam params.mobile string required. Example: 0987654321
     * @bodyParam token string required User Token.
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @response {
     * "status" : false,
     * "code" : 403,
     * "messages" : "Access denied",
     * "response" : null
     * }
     */
    public function profileUpdate(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.fullname' => 'required|string|max:255',
            'params.email' => 'required|string|max:255|email',
            'params.mobile' => 'required|string|max:11',
        ]);
        $params = $inputs['params'];
        $params['user_id'] = auth()->user()->id;
        $params['updated_by'] = auth()->user()->id;
        $this->authorize('profileUpdate', ['App\Models\User', $params]);
        return (new UserTransaction())->update($params,true);
    }

    /**
     * Profile change password
     *
     * Đổi mật khẩu khi đã đăng nhập
     *
     * @bodyParam params.current_password string required. Example: 123456
     * @bodyParam params.new_password string required. Example: 654321"
     * @bodyParam token string required User Token.
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @response {
     * "status" : false,
     * "code" : 403,
     * "messages" : "Access denied",
     * "response" : null
     * }
     */
    public function profileChangePassword(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.current_password' => 'required|string|max:20',
            'params.new_password' => 'required|string|max:20',
        ]);
        $params = $inputs['params'];
        $params['user_id'] = auth()->user()->id;
        $params['updated_by'] = auth()->user()->id;
        $this->authorize('profileChangePassword', ['App\Models\User', $params]);
        return (new UserTransaction())->changePassword($params,true);
    }

    /**
     * User update role
     *
     * Cập nhật quyền cho người dùng
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam token string required User Token.
     * @bodyParam params.user_id integer required user.id của tài khoản muốn cập nhật quyền. Example: 1
     * @bodyParam params.role_ids.* integer required danh sách các quyền người dùng.
     * @response {
     *  "message" : "",
     *  "response" : null
     * }
     */
    public function updateRole(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.user_id' => 'required|integer',
            'params.role_ids' => 'required|integer_array'
        ]);
        $this->authorize('updateRole', ['App\Models\User', $inputs['params']]);
        return (new UserTransaction())->updateRole([
            'user_id' => $inputs['params']['user_id'],
            'role_ids' => $inputs['params']['role_ids'],
            'created_by' => auth()->user()->id,
        ], true);
    }

    /**
     * User request reset password
     *
     * Gửi yêu cầu đặt lại mật khẩu
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam params.email string required. Example: phuonglh@hiworld.com.vn
     *
     * @response {
     * "status" : false,
     * "code" : 403,
     * "messages" : "Access denied",
     * "response" : null
     * }
     */
    public function requestResetPassword(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.email' => 'required|string|email|max:255'
        ]);
        return (new UserRequestTransaction())->create($inputs['params'], true);
    }

    /**
     * User verify checksum reset password
     *
     * Xác thực URL yêu cầu đặt lại mật khẩu
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam params.email string required. Example: phuonglh@hiworld.com.vn
     * @bodyParam params.checksum string required. Example: wfkdsfhwpeylsdhsdh
     *
     * @response {
     * "status" : false,
     * "code" : 403,
     * "messages" : "Access denied",
     * "response" : null
     * }
     */
    public function verifyChecksumResetPassword(Request $request)
    {
       $inputs = $this->validate($request, [
          'params.email' => 'required|string|email|max:255',
          'params.checksum' => 'required|string'
       ]);
       $params=$inputs['params'];
       $user = User::where('email', $params['email'])->first();
       if (isset($user->email)) {
           $user_request = UserRequest::where('user_id',$user->id)->first();
           if ($user_request->checksum == $params['checksum']) {
               $time_now = Carbon::now();
                if ($user_request->expired_at > $time_now) {
                    return $this->responseSuccess([
                        'otp' => $user_request->otp
                    ]);
                }else {
                    $this->error('Yêu cầu đã hết hạn xác thực');
                }
           } else{
               $this->error('Đã hết hạn xác thực');
           }
       } else {
           $this->error('Yêu cầu không tồn tại');
       }
    }

    /**
     * User verify otp reset password
     *
     * Xác thực OTP yêu cầu đặt lại mật khẩu
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam params.email string required. Example: phuonglh@hiworld.com.vn
     * @bodyParam params.otp string required. Example: 666666
     * @bodyParam params.new_password string required. Example: 654321"
     *
     * @response {
     * "status" : false,
     * "code" : 403,
     * "messages" : "Access denied",
     * "response" : null
     * }
     */
    public function verifyOtpResetPassword(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.email' => 'required|string|email|max:255',
            'params.otp' => 'required|string',
            'params.new_password' => 'required|string'
        ]);
        $params = $inputs['params'];
//        dd($params);
        $user = User::where('email', $params['email'])->first();
        if (isset($user->email)) {
            $user_request = UserRequest::where('user_id',$user->id)->first();
            $params['user_id'] = $user_request->user_id;
            if($user_request->otp == $params['otp']) {
                if($user->isActive()) {
                    $result = (new UserTransaction)->updatePassword($params, true);
                } else {
                    $this->error('Tài khoản đang bị khóa');
                }
            } else{
                $this->error('Yêu cầu không tồn tại');
            }
        }else {
            $this->error('Yêu cầu không tồn tại');
        }
    }

    /**
     * User change password
     *
     * Đổi mật khẩu khi đã đăng nhập
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required User Token.
     * @bodyParam params.user_id integer required. Example: 1
     * @bodyParam params.current_password string required. Example: 123456
     * @bodyParam params.new_password string required. Example: 654321
     * @response {
     * "status" : false,
     * "code" : 403,
     * "messages" : "Access denied",
     * "response" : null
     * }
     */
    public function changePassword(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.user_id' => 'required|integer',
            'params.current_password' => 'required|string|max:20',
            'params.new_password' => 'required|string|max:20',
        ]);
        $params = $inputs['params'];
        $params['updated_by'] = auth()->user()->id;
        $this->authorize('update', ['App\Models\User', $params]);
        return (new UserTransaction())->changePassword($params, true);
    }

    /**
     * User detail
     *
     * Xem chi tiết thông tin người dùng
     *
     * @bodyPram env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.user_id integer required User ID. Example: 1
     *
     * @response {
     * "status" : false,
     * "code" : 403,
     * "messages" : "Access denied",
     * "response" : null
     * }
     */
    public function detail(Request $request)
    {
        $inputs = $this->validate($request, [
           'params.user_id' => 'required|integer'
        ]);
        $this->authorize('view', ['App\Models\User', $inputs['params']]);
        $user = User::with('roles:roles.id,roles.name,roles.code,roles.role_group_id')->where('id', $inputs['params']['user_id'])->first();
        if ($user) {
            return $this->responseSuccess($user);
        } else {
            $this->error('Tài khoản người dùng không tồn tại');
        }
    }

    /**
     * User get list
     *
     * Lấy danh sách người dùng
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @response {
     * "status" : false,
     * "code" : 403,
     * "messages" : "Access denied",
     * "response" : null
     * }
     */
    public function list(Request $request)
    {
        $query_builder = User::query();
        $data = $this->getData($request, $query_builder, '\App\Http\Resources\UserResource');
        return $this->responseSuccess($data);
    }

    /**
     * User create
     *
     * Tạo tài khoản đăng nhập cho người dùng
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam params.user_group_id integer required
     * @bodyParam params.ref_id integer required
     * @bodyParam params.username string required. Example: phuonglh
     * @bodyParam params.fullname string required. Example: Nguyen Van A
     * @bodyParam params.email string required. Example: phuonglh@hiworld.com.vn
     * @bodyParam params.mobile string. Example: 0987654321
     * @bodyParam params.status integer. Example: 1
     * @bodyParam token string required User Token.
     *
     * @response {
     * "status" : false,
     * "code" : 403,
     * "messages" : "Access denied",
     * "response" : null
     * }
     */
    public function create(Request $request)
    {
        $inputs = $this->validate($request,[
            'params.user_groupid' => 'required|integer',
            'params.ref_id'        => 'required|integer',
            'params.username'      => 'required|string',
            'params.fullname'      => 'required|string',
            'params.email'         => 'required|string',
            'params.mobile'        => 'required|string|max:20',
            'params.status'        => 'required|integer',
        ]);
        $this->authorize('create', ['App\Models\User', $inputs['params']]);
        return (new UserTransaction())->create($inputs['params'],true);
    }

    /**
     * User update
     *
     * Cập nhật thông tin người dùng
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam params.user_id integer required
     * @bodyParam params.fullname string required. Example: Nguyen Van A
     * @bodyParam params.email string required. Example: phuonglh@hiworld.com.vn
     * @bodyParam params.mobile string. Example: 0987654321
     * @bodyParam token string required User Token.
     *
     * @response {
     * "status" : false,
     * "code" : 403,
     * "messages" : "Access denied",
     * "response" : null
     * }
     */
    public function update(Request $request)
    {
        $inputs = $this->validate($request,[
            'params.user_id'       => 'required|integer',
            'params.fullname'      => 'required|string',
            'params.email'         => 'required|string',
            'params.mobile'        => 'required|string|max:20',
        ]);
        $params = $inputs['params'];
        $params['updated_by'] = auth()->user()->id;
        $this->authorize('update', ['App\Models\User', $params]);
        return (new UserTransaction())->update($params,true);
    }

    /**
     * User lock
     *
     * Khóa người dùng
     *
     *  @bodyParam env string required Enviroment client. Example: web
     *  @bodyParam app_version string required App version. Example: 1.1
     *  @bodyParam lang string required Language. Example: vi
     *  @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     *  @bodyParam params.user_id integer required User ID. Example: 1
     *
     * @response {
     * "status" : false,
     * "code" : 403,
     * "messages" : "Access denied",
     * "response" : null
     * }
     */
    public function lock(Request $request)
    {
        $inputs = $this->validate($request,[
            'params.user_id'       => 'required|integer',
        ]);
        $this->authorize('update', ['App\Models\User', $inputs['params']]);
        return (new UserTransaction())->lock([
            'user_id' => $inputs['params']['user_id'],
            'updated_by' => auth()->user()->id
        ],true);
    }

    /**
     * User active
     *
     * Mở khóa người dùng
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.user_id integer required User ID. Example: 1

     *
     * @response {
     * "status" : false,
     * "code" : 403,
     * "messages" : "Access denied",
     * "response" : null
     * }
     */
    public function active(Request $request)
    {
        $inputs = $this->validate($request,[
            'params.user_id'       => 'required|integer',
        ]);
        $this->authorize('update', ['App\Models\User', $inputs['params']]);
        return (new UserTransaction())->active([
            'user_id' => $inputs['params']['user_id'],
            'updated_by' => auth()->user()->id
        ],true);
    }

    /**
     * User get ref list
     *
     * Lấy danh sách tham chiếu đến người dùng(NĐT,ĐLPP,CTQLQ)
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.user_group_id integer required user_group_id. Example: 1
     *
     * @response {
     * "status": true,
     * "code": 200,
     * "messages": "Success",
     * "response": {
     * user_group: {
     * name: "Quản trị",
     * code: "ADMIN"
     * },
     * ref_list: []
     * }
     * }
     */
    public function getRefList(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.user_group_id' => 'required',
        ]);
        $params = $inputs['params'];
        $user_group = UserGroup::where('id', $params['user_group_id'])->first();
        if ($user_group) {
            switch ($user_group->code) {
                case UserGroup::GROUP_ADMIN:
                    return $this->responseSuccess([
                        'user_group_name' => $user_group->name,
                        'user_group_code' => $user_group->code,
                        'refs' => null
                    ]);
                case UserGroup::GROUP_FUND_COMPANY:
                    $companies = FundCompany::select('id','name')->where('status', FundCompany::STATUS_ACTIVE)->get();//Get db fund_company , status = 1
                    return $this->responseSuccess([
                        'user_group_name' => $user_group->name,
                        'user_group_code' => $user_group->code,
                        'refs' => FundCompany::getArrayForSelectBox($companies, 'id', 'name')
                    ]);
                case UserGroup::GROUP_FUND_DISTRIBUTOR:
                    $distributors = FundDistributor::select('id','name')->where('status', FundDistributor::STATUS_ACTIVE)->get(); //Get db list_fund_distributors , status = 1
                    return $this->responseSuccess([
                        'user_group_name' => $user_group->name,
                        'user_group_code' => $user_group->code,
                        'refs' => FundDistributor::getArrayForSelectBox($distributors, 'id', 'name')
                    ]);
                case UserGroup::GROUP_FUND_DISTRIBUTOR_STAFF:
                    $distributor_staffs = FundDistributorStaff::select('id','name')->where('status', FundDistributorStaff::STATUS_ACTIVE)->get(); //Get db fund_distributor_staffs , status = 1
                    return $this->responseSuccess([
                        'user_group_name' => $user_group->name,
                        'user_group_code' => $user_group->code,
                        'refs' => FundDistributor::getArrayForSelectBox($distributor_staffs, 'id', 'name')
                    ]);
                case UserGroup::GROUP_INVESTOR:
                    $investors = Investor::select('id','name')->get(); //Get db investors , status = 1
                    return $this->responseSuccess([
                        'user_group_name' => $user_group->name,
                        'user_group_code' => $user_group->code,
                        'refs' => FundDistributor::getArrayForSelectBox($investors, 'id', 'name')
                    ]);
            }
        }else {
            $this->error('Nhóm người dùng này không tồn tại');
        }
    }

    /**
     * User get group list
     *
     * Lấy danh sách nhóm người dùng
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     *
     * @response {
     * "status": true,
     * "code": 200,
     * "messages": "Success",
     * "response": {
     * }
     */
    public function getUserGroupList()
    {
        $user_groups = UserGroup::where('status', UserGroup::STATUS_ACTIVE)->get();
        return $this->responseSuccess(UserGroup::getArrayForSelectBox($user_groups, 'id', 'name'));
    }

    /**
     * User get status
     *
     * Lấy danh sách trạng thái người dùng
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     *
     * @response {
     * "status": true,
     * "code": 200,
     * "messages": "Success",
     * "response": {
     * }
     */
    public function getStatus()
    {
        $list_status = User::getListStatus();
        return $this->responseSuccess(User::getArrayForSelectBox($list_status));
    }

    /**
     * User reset password
     *
     * Đổi mật khẩu khi đã đăng nhập
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required User Token.
     * @bodyParam params.user_id integer required. Example: 1
     * @response {
     * "status" : false,
     * "code" : 403,
     * "messages" : "Access denied",
     * "response" : null
     * }
     */
    public function resetPassword(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.user_id' => 'required|integer',
        ]);
        $params = $inputs['params'];
        $params['updated_by'] = auth()->user()->id;
        $this->authorize('update', ['App\Models\User', $params]);
        return (new UserTransaction())->resetPassword($params, true);
    }
}
