<?php


namespace App\Http\Controllers;

use App\Http\Controllers\Traits\TableTrait;
use App\Http\Resources\FundProductTypeResource;
use App\Models\FundProduct;
use App\Models\FundProductType;
use App\Services\Transactions\FundProductTypeTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


/**
 * @group  FundProduct
 */
class FundProductTypeController extends Controller
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
     * Fund product list product type
     *
     * Danh sách loại sản phẩm quỹ
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam params.name required string. Example: Loại SIP
     * @bodyParam params.code required string. Example: SIP
     * @bodyParam params.status required integer. Example: 1
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     *
     * "@response {
     *  "status": true,
     *  "code": 200,
     *  "messages": "Success",
     *  "response": null
     *  }
     */
    public function list(Request $request)
    {
        $query_builder = FundProductType::query();
        $data = $this->getData($request, $query_builder, '\App\Http\Resources\FundProductTypeResource');
        return $this->responseSuccess($data);
    }

    /**
     * Fund product type get list
     *
     * Lấy danh sách loại quỹ
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
        $fund_product_type = FundProductType::where('status', FundProductType::STATUS_ACTIVE)->get();
        return $this->responseSuccess(FundProductType::getArrayForSelectBox($fund_product_type, 'id', 'name'));
    }

    /**
     *Fund product add product type
     *
     * Thêm loại sản phẩm quỹ
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.name_fund_product_type string required Name product type. Example: SIP
     * @bodyParam params.code string required Code product type. Example: SIP
     * @bodyParam params.description string Description. Example:
     *
     * @response {
     * "status": true,
     * "code": 200,
     * "messages": "Success",
     * "response": {fund_product_type_id}
     * }
     */
    public function add(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.name' => 'required|string',
            'params.code' => 'required|string',
            'params.description' => 'string',
            'params.status'        => 'required|integer',
        ]);
        $inputs['params']['created_by'] = '';
        $user = Auth::user();
        $inputs['params']['created_by'] = $user->id;
        return (new FundProductTypeTransaction())->create($inputs['params'], true);
    }

    /**
     * Fund product update product type
     *
     * Cập nhật loại sản phẩm quỹ
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.fund_product_type_id integer required ID product type. Example: 1
     * @bodyParam params.name_fund_product_type string required Name product type. Example: SIP
     * @bodyParam params.description string Description. Example:
     *
     * @response {
     *  "status": true,
     *  "code": 200,
     *  "messages": "Success",
     *  "response": null
     * }
     */
    public function update(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.fund_product_type_id' => 'required|integer',
            'params.name' => 'required|string',
            'params.description' => 'string'
        ]);
        $inputs['params']['updated_by'] = '';
        $user = Auth::user();
        $inputs['params']['updated_by'] = $user->id;
        return (new FundProductTypeTransaction())->update($inputs['params'], true);
    }

    /**
     * Fund product lock product type
     *
     * Khóa loại sản phẩm quỹ
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.fund_product_type_id integer required ID product type. Example: 1
     *
     * "@response {
     *  "status": true,
     *  "code": 200,
     *  "messages": "Success",
     *  "response": null
     * }
     */
    public function lock(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.fund_product_type_id' => 'required|integer'
        ]);
        return (new FundProductTypeTransaction())->lock($inputs['params'], true);
    }

    /**
     * Fund product lock product type
     *
     * Khóa loại sản phẩm quỹ
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.fund_product_type_id integer required ID product type. Example: 1
     *
     * "@response {
     *  "status": true,
     *  "code": 200,
     *  "messages": "Success",
     *  "response": null
     * }
     */
    public function active(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.fund_product_type_id' => 'required|integer'
        ]);
        return (new FundProductTypeTransaction())->active($inputs['params'], true);
    }

    /**
     * Fund Product Type detail
     *
     * Xem chi tiết thông tin loại sản phẩm quỹ
     *
     * @bodyPram env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.fund_product_type_id integer required User ID. Example: 1
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
            'params.fund_product_type_id' => 'required|integer'
        ]);
        $fund_product_type = FundProductType::where('id', $inputs['params']['fund_product_type_id'])->first();
        if ($fund_product_type) {
                return $this->responseSuccess($fund_product_type);
        } else {
            $this->error('Loại sản phẩm quỹ không tồn tại');
        }
    }

    /**
     * Fund Product Type get status
     *
     * Lấy danh sách trạng thái Loại sản phẩm quỹ
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
        $list_status = FundProductType::getListStatus();
        return $this->responseSuccess(FundProductType::getArrayForSelectBox($list_status));
    }

}
