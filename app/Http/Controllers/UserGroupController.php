<?php


namespace App\Http\Controllers;

use App\Models\UserGroup;
use App\Services\Transactions\UserGroupTransaction;
use Illuminate\Http\Request;

/**
 * @group  UserApi
 */
class UserGroupController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['login', 'requestResetPassword', 'verifyChecksumResetPassword', 'verifyOtpResetPassword' ] ]);
    }

    /**
     * User group create
     *
     * Tạo nhóm người dùng
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.parent_id integer required. Example: 0
     * @bodyParam params.name string required. Example: Admin
     * @bodyParam params.code string required. Example: ADMIN
     * @bodyParam params.status integer required. Example: 1
     *
     * @response {
     *   "status": true,
     *   "code": 200,
     *   "messages": "Success",
     *   "response": {
     *      user_group_id: 1
     *   }
     * }
     */
    public function create(Request $request)
    {
        $inputs = $this->validate($request, [
           'params.parent_id' => 'required|integer',
           'params.name'      => 'required|string',
           'params.code'      => 'required|string',
           'params.status'    => 'required|integer'
        ]);
        $this->authorize('create', ['App\Models\UserGroup', $inputs['params'] ]);
        return (new UserGroupTransaction())->create($inputs['params'],true);
    }

    /**
     * User group update
     *
     * Cập nhật nhóm người dùng
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.user_group_id integer required. Example: 1
     * @bodyParam params.name string required. Example: Admin
     * @bodyParam params.code string required. Example: ADMIN
     * @bodyParam params.status integer required. Example: 1
     *
     */
    public function update(Request $request)
    {
        $inputs = $this->validate($request, [
           'params.user_group_id' => 'required|integer',
           'params.name'          => 'required|string',
           'params.code'          => 'required|string',
           'params.status'        => 'required|integer'
        ]);
        $this->authorize('update', ['App\Models\UserGroup', $inputs['params'] ]);
        return (new UserGroupTransaction())->update($inputs['params'], true);
    }

    /**
     * User group get role list
     *
     * Lấy danh sách quyền của nhóm người dùng
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.user_group_id integer required. Example: 1
     *
     */
    public function getRoleList(Request $request) {
        $inputs = $this->validate($request, [
            'params.user_group_id' => 'required|integer',
        ]);
        $user_group = UserGroup::where('id', $inputs['params']['user_group_id'])->first();
        if ($user_group) {
            $user_group->role_groups = $user_group->getRoleGroups();
            return $this->responseSuccess($user_group);
        }
        $this->error('Nhóm người dùng không tồn tại');
    }
}
