<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\TableTrait;
use App\Services\Transactions\InvestorBankAccountTransaction;
use App\Models\InvestorBankAccount;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


/**
 * @group  Investor
 */
class InvestorBankAccountController extends Controller
{
    use TableTrait;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth', ['except' => ['login', 'requestResetPassword', 'verifyChecksumResetPassword', 'verifyOtpResetPassword']]);
    }

    /**
     * InvestorBankAccount Create
     *
     * Tạo tài khoản ngần hàng cho nhà đầu tư
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.investor_id integer required Investor ID. Example: 1
     * @bodyParam params.bank_id integer required Bank ID. Example: 1
     * @bodyParam params.account_holder string required account_holder. Example: NGUYEN VAN A
     * @bodyParam params.account_number string required account_number. Example: 34986903247902
     * @bodyParam params.branch string required Branch. Example: Dong Do
     * @bodyParam params.description string required Desciprtion. Example: Tai khoan ngan hang
     * @bodyParam params.default integer required Default. Example: 1
     * @bodyParam params.status integer required Status. Example: 1
     * @response {
     *      "status": true,
     *      "code": 200,
     *      "messages": "Success",
     *      "response": {
     *          "investor_bank_account_id" : 1
     *      }
     * }
     */
    public function create(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.investor_id' => 'required|integer',
            'params.bank_id' => 'required|integer',
            'params.account_holder' => 'required|string|max:255',
            'params.account_number' => 'required|string|max:50',
            'params.branch' => 'string|max:255',
            'params.description' => 'string|max:500',
            'params.is_default' => 'required|integer',
            'params.status' => 'required|integer',
        ]);
        $params = $inputs['params'];
        return (new InvestorBankAccountTransaction())->create([
            'investor_id' => $params['investor_id'],
            'bank_id' => $params['bank_id'],
            'account_holder' => $params['account_holder'],
            'account_number' => $params['account_number'],
            'branch' => $params['branch'],
            'description' => $params['description'],
            'is_default' => $params['is_default'],
            'status' => $params['status'],
            'created_by' => auth()->user()->id,
        ], true);
    }

    /**
     * InvestorBankAccount getStatus
     *
     * Lấy danh sách trạng thái sản phẩm đại lý phân phối
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
        $status = [
            ['title' => trans('status.investor_bank_account.active'), 'value' => InvestorBankAccount::STATUS_ACTIVE],
            ['title' => trans('status.investor_bank_account.lock'), 'value' => InvestorBankAccount::STATUS_LOCK],
        ];
        return $this->responseSuccess($status);
    }

    /**
     * InvestorBankAccount list
     *
     * Lấy danh sách sản phẩm đại lý phân phối
     * 
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
        $query_builder = InvestorBankAccount::with([
            'fund_company:id,name',
            'fund_distributor:id,name',
            'investor:id,name',
            'bank:id,name'
        ]);   
        return $this->responseSuccess($this->getData($request, $query_builder));
    }

    /**
     * InvestorBankAccount lock
     *
     * Khóa sản phẩm đại lý phân phối
     *
     *  @bodyParam env string required Enviroment client. Example: web
     *  @bodyParam app_version string required App version. Example: 1.1
     *  @bodyParam lang string required Language. Example: vi
     *  @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     *  @bodyParam params.investor_bank_account_id integer required InvestorBankAccount ID. Example: 1
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
            'params.investor_bank_account_id'       => 'required|integer',
        ]);
        $this->authorize('update', ['App\Models\InvestorBankAccount', $inputs['params']]);
        return (new InvestorBankAccountTransaction())->lock([
            'investor_bank_account_id' => $inputs['params']['investor_bank_account_id'],
            'updated_by' => auth()->user()->id
        ],true);
    }

    /**
     * InvestorBankAccount active
     *
     * Mở khóa đại lý phân phối
     *
     *  @bodyParam env string required Enviroment client. Example: web
     *  @bodyParam app_version string required App version. Example: 1.1
     *  @bodyParam lang string required Language. Example: vi
     *  @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     *  @bodyParam params.investor_bank_account_id integer required FundDistributor ID. Example: 1
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
            'params.investor_bank_account_id'       => 'required|integer',
        ]);
        $this->authorize('update', ['App\Models\InvestorBankAccount', $inputs['params']]);
        return (new InvestorBankAccountTransaction())->active([
            'investor_bank_account_id' => $inputs['params']['investor_bank_account_id'],
            'updated_by' => auth()->user()->id
        ],true);
    }

    /**
     * InvestorBankAccount active
     *
     * Mở khóa đại lý phân phối
     *
     *  @bodyParam env string required Enviroment client. Example: web
     *  @bodyParam app_version string required App version. Example: 1.1
     *  @bodyParam lang string required Language. Example: vi
     *  @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     *  @bodyParam params.investor_bank_account_id integer required FundDistributor ID. Example: 1
     *
     * @response {
     * "status" : false,
     * "code" : 403,
     * "messages" : "Access denied",
     * "response" : null
     * }
     */
    public function setDefault(Request $request)
    {
        $inputs = $this->validate($request,[
            'params.investor_bank_account_id'       => 'required|integer',
        ]);
        $this->authorize('update', ['App\Models\InvestorBankAccount', $inputs['params']]);
        return (new InvestorBankAccountTransaction())->setDefault([
            'investor_bank_account_id' => $inputs['params']['investor_bank_account_id'],
            'updated_by' => auth()->user()->id
        ],true);
    }

    /**
     * InvestorBankAccount detail
     *
     * Xem chi tiết thông tin đại lý phân phối
     *
     * @bodyPram env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.investor_bank_account_id integer required FundDistributor ID. Example: 1
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
           'params.investor_bank_account_id' => 'required|integer'
        ]);
        $this->authorize('view', ['App\Models\InvestorBankAccount', $inputs['params']]);
        $row = InvestorBankAccount::with([
                'fund_distributor:id,name',
                'fund_product:id,name',
                'fund_distributor_bank_account' => function($query) {
                    $query->with('supervising_bank:id,name,trade_name')
                        ->select(['id','account_holder','account_number','branch','supervising_bank_id']);
                }
            ])->where('id', $inputs['params']['investor_bank_account_id'])->first();
        if ($row) {
            return $this->responseSuccess($row);
        } else {
            $this->error('Sản phẩm đại lý phân phối không tồn tại');
        }
    }
}
