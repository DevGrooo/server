<?php

namespace App\Http\Controllers;

use App\Models\FundDistributor;
use App\Services\Transactions\FundDistributorTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


/**
 * @group  FundDistributor
 */
class FundDistributorController extends Controller
{
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
     * FundDistributor Create
     *
     * Tạo đại lý phân phối
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.fund_company_id integer required ID fund company. Example: 1
     * @bodyParam params.name string required Name distributor. Example: Mira Access 1
     * @bodyParam params.code string required Code distributor. Example: MIRAACCESS1
     * @bodyParam params.license_number string License Numer. Example: 
     * @bodyParam params.issuing_date string issuing_date. Example: 01/01/2020
     * @bodyParam params.head_office string head_office. Example: 
     * @bodyParam params.phone string phone. Example: 0987654321
     * @bodyParam params.website string website. Example: 
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
            'params.fund_company_id' => 'required|integer',
            'params.name' => 'required|string|max:500',
            'params.code' => 'required|string|max:255',
            'params.license_number' => 'string|max:255',
            'params.issuing_date' => 'date_format:d/m/Y',
            'params.head_office' => 'string',
            'params.phone' => 'string|max:50',
            'params.website' => 'string|max:255',
            'params.status' => 'required|integer',
        ]);
        $params = $inputs['params'];
        $params['created_by'] = auth()->user()->id;
        $this->authorize('create', ['App\Models\FundDistributor', $params]);
        return (new FundDistributorTransaction())->create($params, true);
    }

    /**
     * FundDistributor Update
     *
     * Cập nhật đại lý phân phối
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.name string required Name distributor. Example: Mira Access 1
     * @bodyParam params.code string required Code distributor. Example: MIRAACCESS1
     * @bodyParam params.license_number string License Numer. Example: 
     * @bodyParam params.issuing_date string issuing_date. Example: 01/01/2020
     * @bodyParam params.head_office string head_office. Example: 
     * @bodyParam params.phone string phone. Example: 0987654321
     * @bodyParam params.website string website. Example: 
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
            'params.name' => 'required|string|max:500',
            'params.code' => 'required|string|max:255',
            'params.license_number' => 'string|max:255',
            'params.issuing_date' => 'date_format:d/m/Y',
            'params.head_office' => 'string',
            'params.phone' => 'string|max:50',
            'params.website' => 'string|max:255',
        ]);
        $params = $inputs['params'];
        $params['updated_by'] = auth()->user()->id;
        $this->authorize('update', ['App\Models\FundDistributor', $params]);
        return (new FundDistributorTransaction())->update($params, true);
    }

    /**
     * Fund distribute get status
     *
     * Lấy danh sách trạng thái đại lý phân phối
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
            ['title' => trans('status.fund_distributor.active'), 'value' => FundDistributor::STATUS_ACTIVE],
            ['title' => trans('status.fund_distributor.lock'), 'value' => FundDistributor::STATUS_LOCK],
        ];
        return $this->responseSuccess($status);
    }

    /**
     * FundDistributor list
     *
     * Lấy danh sách đại lý phân phối
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
    public function list()
    {
        $rows = FundDistributor::with('fund_company:fund_company.id,fund_company.name')->get()->toArray();
        $list = [
            'data' => $rows,
            'count' => count($rows)
        ];
        return $this->responseSuccess($list);
    }

    /**
     * FundDistributor lock
     *
     * Khóa đại lý phân phối
     *
     *  @bodyParam env string required Enviroment client. Example: web
     *  @bodyParam app_version string required App version. Example: 1.1
     *  @bodyParam lang string required Language. Example: vi
     *  @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     *  @bodyParam params.fund_distributor_id integer required FundDistributor ID. Example: 1
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
            'params.fund_distributor_id'       => 'required|integer',
        ]);
        $this->authorize('update', ['App\Models\FundDistributor', $inputs['params']]);
        return (new FundDistributorTransaction())->lock([
            'fund_distributor_id' => $inputs['params']['fund_distributor_id'],
            'updated_by' => auth()->user()->id
        ],true);
    }

    /**
     * FundDistributor active
     *
     * Mở khóa đại lý phân phối
     *
     *  @bodyParam env string required Enviroment client. Example: web
     *  @bodyParam app_version string required App version. Example: 1.1
     *  @bodyParam lang string required Language. Example: vi
     *  @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     *  @bodyParam params.fund_distributor_id integer required FundDistributor ID. Example: 1
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
            'params.fund_distributor_id'       => 'required|integer',
        ]);
        $this->authorize('update', ['App\Models\FundDistributor', $inputs['params']]);
        return (new FundDistributorTransaction())->active([
            'fund_distributor_id' => $inputs['params']['fund_distributor_id'],
            'updated_by' => auth()->user()->id
        ],true);
    }

    /**
     * FundDistributor detail
     *
     * Xem chi tiết thông tin đại lý phân phối
     *
     * @bodyPram env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.fund_distributor_id integer required FundDistributor ID. Example: 1
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
           'params.fund_distributor_id' => 'required|integer'
        ]);
        $this->authorize('view', ['App\Models\FundDistributor', $inputs['params']]);
        $row = FundDistributor::with('fund_company:fund_company.id,fund_company.name')->where('id', $inputs['params']['fund_distributor_id'])->first();
        if ($row) {
            return $this->responseSuccess($row);
        } else {
            $this->error('Đại lý phân phối không tồn tại');
        }
    }

    /**
     * FundDistributor get list
     *
     * Lấy danh sách đại lý phân phối
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
    public function getList()
    {
        $rows = FundDistributor::where('status', FundDistributor::STATUS_ACTIVE)->get();
        return $this->responseSuccess(FundDistributor::getArrayForSelectBox($rows, 'id', 'name'));
    }

    /**
     * FundDistributor get list code
     *
     * Lấy danh sách đại lý phân phối theo mã
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
    public function getListCode()
    {
        $rows = FundDistributor::where('status', FundDistributor::STATUS_ACTIVE)->get();
        return $this->responseSuccess(FundDistributor::getArrayForSelectBox($rows, 'code', 'name'));
    }
}
