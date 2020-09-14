<?php

namespace App\Http\Controllers;

use App\Models\FundDistributorLocation;
use App\Services\Transactions\FundDistributorLocationTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


/**
 * @group  FundDistributor
 */
class FundDistributorLocationController extends Controller
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
     * FundDistributorLocation Create
     *
     * Thêm Chi nhánh cho đại lý phân phối
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.fund_distributor_id integer required ID fund company. Example: 1
     * @bodyParam params.name string required Name. Example: Mira Access 1
     * @bodyParam params.address string required address. Example: MIRAACCESS1
     * @bodyParam params.phone string phone. Example: 
     * @bodyParam params.fax string fax. Example: 
     * @bodyParam params.status required integer status. Example: 1
     * @response {
     *      "status": true,
     *      "code": 200,
     *      "messages": "Success",
     *      "response": {
     *          "fund_distributor_location_id" : 1,
     *      }
     * }
     */
    public function create(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.fund_distributor_id' => 'required|integer',
            'params.name' => 'required|string|max:255',
            'params.address' => 'string|max:500',
            'params.phone' => 'string|max:255',
            'params.fax' => 'string|max:255',
            'params.status' => 'required|integer',
        ]);
        $params = $inputs['params'];
        $params['created_by'] = auth()->user()->id;
        $this->authorize('create', ['App\Models\FundDistributorLocation', $params]);
        return (new FundDistributorLocationTransaction())->create($params, true);
    }

    /**
     * FundDistributorLocation update
     *
     * Cập nhật Chi nhánh cho đại lý phân phối
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.fund_distributor_location_id integer required ID fund distributor location. Example: 1
     * @bodyParam params.name string required Name. Example: Mira Access 1
     * @bodyParam params.address string required address. Example: MIRAACCESS1
     * @bodyParam params.phone string phone. Example: 
     * @bodyParam params.fax string fax. Example:     
     * @response {
     *      "status": true,
     *      "code": 200,
     *      "messages": "Success",
     *      "response": {
     *         
     *      }
     * }
     */
    public function update(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.fund_distributor_location_id' => 'required|integer',
            'params.name' => 'required|string|max:255',
            'params.account_number' => 'string|max:500',
            'params.phone' => 'string|max:255',
            'params.fax' => 'string|max:255',
        ]);
        $params = $inputs['params'];
        $params['updated_by'] = auth()->user()->id;
        $this->authorize('update', ['App\Models\FundDistributorLocation', $params]);
        return (new FundDistributorLocationTransaction())->update($params, true);
    }

    /**
     * FundDistributeLocation getStatus
     *
     * Lấy danh sách trạng thái Chi nhánh đại lý phân phối
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
            ['title' => trans('status.fund_distributor_location.active'), 'value' => FundDistributorLocation::STATUS_ACTIVE],
            ['title' => trans('status.fund_distributor_location.lock'), 'value' => FundDistributorLocation::STATUS_LOCK],
        ];
        return $this->responseSuccess($status);
    }

    /**
     * FundDistributeLocation list
     *
     * Lấy danh sách Chi nhánh đại lý phân phối
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
        $rows = FundDistributorLocation::with([
            'fund_company:id,name',
            'fund_distributor:id,name'
        ])->get()->toArray();
        $list = [
            'data' => $rows,
            'count' => count($rows)
        ];
        return $this->responseSuccess($list);
    }

    /**
     * FundDistributorLocation lock
     *
     * Khóa Chi nhánh đại lý phân phối
     *
     *  @bodyParam env string required Enviroment client. Example: web
     *  @bodyParam app_version string required App version. Example: 1.1
     *  @bodyParam lang string required Language. Example: vi
     *  @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     *  @bodyParam params.fund_distributor_location_id integer required FundDistributorLocation ID. Example: 1
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
            'params.fund_distributor_location_id'       => 'required|integer',
        ]);
        $this->authorize('update', ['App\Models\FundDistributorLocation', $inputs['params']]);
        return (new FundDistributorLocationTransaction())->lock([
            'fund_distributor_location_id' => $inputs['params']['fund_distributor_location_id'],
            'updated_by' => auth()->user()->id
        ],true);
    }

    /**
     * FundDistributorLocation active
     *
     * Mở khóa đại lý phân phối
     *
     *  @bodyParam env string required Enviroment client. Example: web
     *  @bodyParam app_version string required App version. Example: 1.1
     *  @bodyParam lang string required Language. Example: vi
     *  @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     *  @bodyParam params.fund_distributor_location_id integer required FundDistributor ID. Example: 1
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
            'params.fund_distributor_location_id'       => 'required|integer',
        ]);
        $this->authorize('update', ['App\Models\FundDistributorLocation', $inputs['params']]);
        return (new FundDistributorLocationTransaction())->active([
            'fund_distributor_location_id' => $inputs['params']['fund_distributor_location_id'],
            'updated_by' => auth()->user()->id
        ],true);
    }

    /**
     * FundDistributorLocation detail
     *
     * Xem chi tiết thông tin đại lý phân phối
     *
     * @bodyPram env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.fund_distributor_location_id integer required FundDistributor ID. Example: 1
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
           'params.fund_distributor_location_id' => 'required|integer'
        ]);
        $this->authorize('view', ['App\Models\FundDistributorLocation', $inputs['params']]);
        $row = FundDistributorLocation::with([
                'fund_distributor:id,name'
            ])->where('id', $inputs['params']['fund_distributor_location_id'])->first();
        if ($row) {
            return $this->responseSuccess($row);
        } else {
            $this->error('Chi nhánh đại lý phân phối không tồn tại');
        }
    }
}
