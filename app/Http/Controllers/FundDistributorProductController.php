<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\TableTrait;
use App\Models\FundDistributor;
use App\Models\FundDistributorProduct;
use App\Services\Transactions\FundDistributorProductTransaction;
use App\Services\Transactions\FundDistributorTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


/**
 * @group  FundDistributor
 */
class FundDistributorProductController extends Controller
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
     * FundDistributorProduct Create
     *
     * Thêm sản phẩm cho đại lý phân phối
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.fund_product_id integer required ID fund company. Example: 1
     * @bodyParam params.fund_distributor_id integer required ID fund company. Example: 1
     * @bodyParam params.supervising_bank_id integer required ID fund company. Example: 1
     * @bodyParam params.account_holder string required Name distributor. Example: Mira Access 1
     * @bodyParam params.account_number string required Code distributor. Example: MIRAACCESS1
     * @bodyParam params.branch string License Numer. Example: 
     * @bodyParam params.status required integer status. Example: 1
     * @response {
     *      "status": true,
     *      "code": 200,
     *      "messages": "Success",
     *      "response": {
     *          "fund_distributor_id" : 1,
     *          "account_commission_id" : 1
     *      }
     * }
     */
    public function create(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.fund_product_id' => 'required|integer',
            'params.fund_distributor_id' => 'required|integer',
            'params.supervising_bank_id' => 'required|integer',
            'params.account_holder' => 'required|string|max:500',
            'params.account_number' => 'required|string|max:255',
            'params.branch' => 'string|max:255',
            'params.status' => 'required|integer',
        ]);
        $params = $inputs['params'];
        $params['created_by'] = auth()->user()->id;
        $this->authorize('create', ['App\Models\FundDistributorProduct', $params]);
        return (new FundDistributorProductTransaction())->create($params, true);
    }

    /**
     * FundDistributorProduct update
     *
     * Cập nhật sản phẩm cho đại lý phân phối
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.fund_distributor_product_id integer required ID fund distributor product. Example: 1
     * @bodyParam params.fund_distributor_bank_account_id integer required ID distributor bank account. Example: 1
     * @bodyParam params.supervising_bank_id integer required ID fund company. Example: 1
     * @bodyParam params.account_holder string required Name distributor. Example: Mira Access 1
     * @bodyParam params.account_number string required Code distributor. Example: MIRAACCESS1
     * @bodyParam params.branch string License Numer. Example:      
     * @response {
     *      "status": true,
     *      "code": 200,
     *      "messages": "Success",
     *      "response": {
     *          "fund_distributor_id" : 1,
     *          "account_commission_id" : 1
     *      }
     * }
     */
    public function update(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.fund_distributor_product_id' => 'required|integer',
            'params.fund_distributor_bank_account_id' => 'required|integer',
            'params.supervising_bank_id' => 'required|integer',
            'params.account_holder' => 'required|string|max:500',
            'params.account_number' => 'required|string|max:255',
            'params.branch' => 'string|max:255',
        ]);
        $params = $inputs['params'];
        $params['updated_by'] = auth()->user()->id;
        $this->authorize('update', ['App\Models\FundDistributorProduct', $params]);
        return (new FundDistributorProductTransaction())->update($params, true);
    }

    /**
     * FundDistributeProduct getStatus
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
            ['title' => trans('status.fund_distributor_product.active'), 'value' => FundDistributorProduct::STATUS_ACTIVE],
            ['title' => trans('status.fund_distributor_product.lock'), 'value' => FundDistributorProduct::STATUS_LOCK],
        ];
        return $this->responseSuccess($status);
    }

    /**
     * FundDistributeProduct list
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
        $query_builder = FundDistributorProduct::with([
            'fund_company:id,name',
            'fund_distributor:id,name',
            'fund_product_type:id,name',
            'fund_product:id,name',
            'fund_distributor_bank_account' => function($query) {
                $query->with('supervising_bank:id,name,trade_name')->select(['id', 'account_holder', 'account_number', 'branch', 'fund_distributor_product_id', 'supervising_bank_id']);
            }
        ]);
        $data = $this->getData($request, $query_builder);
        return $this->responseSuccess($data);
    }

    /**
     * FundDistributorProduct lock
     *
     * Khóa sản phẩm đại lý phân phối
     *
     *  @bodyParam env string required Enviroment client. Example: web
     *  @bodyParam app_version string required App version. Example: 1.1
     *  @bodyParam lang string required Language. Example: vi
     *  @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     *  @bodyParam params.fund_distributor_product_id integer required FundDistributorProduct ID. Example: 1
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
            'params.fund_distributor_product_id'       => 'required|integer',
        ]);
        $this->authorize('update', ['App\Models\FundDistributorProduct', $inputs['params']]);
        return (new FundDistributorProductTransaction())->lock([
            'fund_distributor_product_id' => $inputs['params']['fund_distributor_product_id'],
            'updated_by' => auth()->user()->id
        ],true);
    }

    /**
     * FundDistributorProduct active
     *
     * Mở khóa đại lý phân phối
     *
     *  @bodyParam env string required Enviroment client. Example: web
     *  @bodyParam app_version string required App version. Example: 1.1
     *  @bodyParam lang string required Language. Example: vi
     *  @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     *  @bodyParam params.fund_distributor_product_id integer required FundDistributor ID. Example: 1
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
            'params.fund_distributor_product_id'       => 'required|integer',
        ]);
        $this->authorize('update', ['App\Models\FundDistributorProduct', $inputs['params']]);
        return (new FundDistributorProductTransaction())->active([
            'fund_distributor_product_id' => $inputs['params']['fund_distributor_product_id'],
            'updated_by' => auth()->user()->id
        ],true);
    }

    /**
     * FundDistributorProduct detail
     *
     * Xem chi tiết thông tin đại lý phân phối
     *
     * @bodyPram env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.fund_distributor_product_id integer required FundDistributor ID. Example: 1
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
           'params.fund_distributor_product_id' => 'required|integer'
        ]);
        $this->authorize('view', ['App\Models\FundDistributorProduct', $inputs['params']]);
        $row = FundDistributorProduct::with([
                'fund_distributor:id,name',
                'fund_product:id,name',
                'fund_distributor_bank_account' => function($query) {
                    $query->with('supervising_bank:id,name,trade_name')
                        ->select(['id','account_holder','account_number','branch','supervising_bank_id']);
                }
            ])->where('id', $inputs['params']['fund_distributor_product_id'])->first();
        if ($row) {
            return $this->responseSuccess($row);
        } else {
            $this->error('Sản phẩm đại lý phân phối không tồn tại');
        }
    }
}
