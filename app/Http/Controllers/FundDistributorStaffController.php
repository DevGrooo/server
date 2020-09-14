<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\TableTrait;
use App\Models\FundDistributorStaff;
use App\Services\Transactions\FundDistributorStaffTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @group  FundDistributor
 */
class FundDistributorStaffController extends Controller
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
     * FundDistributorStaff Create
     *
     * Thêm sản phẩm cho đại lý phân phối
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5     
     * @bodyParam params.fund_distributor_id integer required ID fund company. Example: 1
     * @bodyParam params.name integer required Name. Example: 1
     * @bodyParam params.certificate_number string required certificate_number. Example: MIRAACCESS1
     * @bodyParam params.issuing_date string issuing_date. Example: 
     * @bodyParam params.email integer required email. Example: 1
     * @bodyParam params.phone string required phone. Example: Mira Access 1
     * @bodyParam params.status required integer status. Example: 1
     * @response {
     *      "status": true,
     *      "code": 200,
     *      "messages": "Success",
     *      "response": {
     *          "fund_distributor_staff_id" : 1
     *      }
     * }
     */
    public function create(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.fund_distributor_id' => 'required|integer',
            'params.name' => 'required|string|max:255',
            'params.certificate_number' => 'required|string',
            'params.issuing_date' => 'required|string',
            'params.email' => 'string|email',
            'params.phone' => 'string|max:255',
            'params.status' => 'required|integer',
        ]);
        $params = $inputs['params'];
        $params['created_by'] = auth()->user()->id;
        $this->authorize('create', ['App\Models\FundDistributorStaff', $params]);
        return (new FundDistributorStaffTransaction())->create($params, true);
    }

    /**
     * FundDistributorStaff update
     *
     * Cập nhật sản phẩm cho đại lý phân phối
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.fund_distributor_staff_id integer required ID fund distributor product. Example: 1
     * @bodyParam params.name integer required Name. Example: 1
     * @bodyParam params.certificate_number string required certificate_number. Example: MIRAACCESS1
     * @bodyParam params.issuing_date string issuing_date. Example: 
     * @bodyParam params.email integer required email. Example: 1
     * @bodyParam params.phone string required phone. Example: Mira Access 1
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
            'params.fund_distributor_staff_id' => 'required|integer',
            'params.name' => 'required|string|max:255',
            'params.certificate_number' => 'required|string',
            'params.issuing_date' => 'required|string',
            'params.email' => 'string|email',
            'params.phone' => 'string|max:255',
        ]);
        $params = $inputs['params'];
        $params['updated_by'] = auth()->user()->id;
        $this->authorize('update', ['App\Models\FundDistributorStaff', $params]);
        return (new FundDistributorStaffTransaction())->update($params, true);
    }

    /**
     * FundDistributorStaff getStatus
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
            ['title' => trans('status.fund_distributor_staff.active'), 'value' => FundDistributorStaff::STATUS_ACTIVE],
            ['title' => trans('status.fund_distributor_staff.lock'), 'value' => FundDistributorStaff::STATUS_LOCK],
        ];
        return $this->responseSuccess($status);
    }

    /**
     * FundDistributorStaff list
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
        $query_builder = FundDistributorStaff::with([
            'fund_company:id,name',
            'fund_distributor:id,name'
        ]);    
        return $this->responseSuccess($this->getData($request, $query_builder));
    }

    /**
     * FundDistributorStaff lock
     *
     * Khóa sản phẩm đại lý phân phối
     *
     *  @bodyParam env string required Enviroment client. Example: web
     *  @bodyParam app_version string required App version. Example: 1.1
     *  @bodyParam lang string required Language. Example: vi
     *  @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     *  @bodyParam params.fund_distributor_staff_id integer required FundDistributorStaff ID. Example: 1
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
            'params.fund_distributor_staff_id'       => 'required|integer',
        ]);
        $this->authorize('update', ['App\Models\FundDistributorStaff', $inputs['params']]);
        return (new FundDistributorStaffTransaction())->lock([
            'fund_distributor_staff_id' => $inputs['params']['fund_distributor_staff_id'],
            'updated_by' => auth()->user()->id
        ],true);
    }

    /**
     * FundDistributorStaff active
     *
     * Mở khóa đại lý phân phối
     *
     *  @bodyParam env string required Enviroment client. Example: web
     *  @bodyParam app_version string required App version. Example: 1.1
     *  @bodyParam lang string required Language. Example: vi
     *  @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     *  @bodyParam params.fund_distributor_staff_id integer required FundDistributor ID. Example: 1
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
            'params.fund_distributor_staff_id'       => 'required|integer',
        ]);
        $this->authorize('update', ['App\Models\FundDistributorStaff', $inputs['params']]);
        return (new FundDistributorStaffTransaction())->active([
            'fund_distributor_staff_id' => $inputs['params']['fund_distributor_staff_id'],
            'updated_by' => auth()->user()->id
        ],true);
    }

    /**
     * FundDistributorStaff detail
     *
     * Xem chi tiết thông tin đại lý phân phối
     *
     * @bodyPram env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.fund_distributor_staff_id integer required FundDistributor ID. Example: 1
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
           'params.fund_distributor_staff_id' => 'required|integer'
        ]);
        $this->authorize('view', ['App\Models\FundDistributorStaff', $inputs['params']]);
        $row = FundDistributorStaff::with([
                'fund_distributor:id,name'
            ])->where('id', $inputs['params']['fund_distributor_staff_id'])->first();
        if ($row) {
            return $this->responseSuccess($row);
        } else {
            $this->error('Sản phẩm đại lý phân phối không tồn tại');
        }
    }

    /**
     * FundDistributorStaff get list
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
        $rows = FundDistributorStaff::where('status', FundDistributorStaff::STATUS_ACTIVE)->get();
        return $this->responseSuccess(FundDistributorStaff::getArrayForSelectBox($rows, 'id', 'name'));
    }
}
