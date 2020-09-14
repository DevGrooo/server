<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\AccountCommission;
use App\Http\Controllers\Traits\TableTrait;
use App\Services\Transactions\AccountCommissionTransaction;


/**
 * @group  Commission
 */
class AccountCommissionController extends Controller
{

    use TableTrait;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Account Commission lock
     *
     * Khóa tài khoản hoa hồng
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.account_commission_id integer required ID Account Commission. Example: 1
     * @response {
     *      "status": true,
     *      "code": 200,
     *      "messages": "Success",
     *      "response": {
     *          "investor_id" : 1,
     *          "investor_bank_account_id" : 1
     *      }
     * }
     */
    public function lock(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.account_commission_id' => 'required|integer',
        ]);

        $params = $inputs['params'];
        $params['updated_by'] = auth()->user()->id;
        $this->authorize('update', ['App\Models\AccountCommission', $params]);
        return (new AccountCommissionTransaction())->lock($params, true);
    }

    /**
     * Account Commission active
     *
     * Mở khóa tài khoản hoa hồng
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.account_commission_id integer required ID Account Commission. Example: 1
     * @response {
     *      "status": true,
     *      "code": 200,
     *      "messages": "Success",
     *      "response": {
     *          "investor_id" : 1,
     *          "investor_bank_account_id" : 1
     *      }
     * }
     */
    public function active(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.account_commission_id' => 'required|integer',
        ]);
        $params = $inputs['params'];
        $params['updated_by'] = auth()->user()->id;
        $this->authorize('update', ['App\Models\AccountCommission', $params]);
        return (new AccountCommissionTransaction())->active($params, true);
    }

    /**
     * Account Commission list
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     *
     * @response {
     * "status" : true,
     * "code" : 200,
     * "messages" : "Success",
     * "response" : null
     * }
     */
    public function list(Request $request)
    {
        $data = $this->getData(
            $request,
            AccountCommission::query(),
            '\App\Http\Resources\AccountCommissionResource',
            ['id', 'ref_id', 'ref_type', 'balance', 'balance_available', 'balance_freezing', 'balance_waiting', 'status']
        );

        return $this->responseSuccess($data);
    }
}
