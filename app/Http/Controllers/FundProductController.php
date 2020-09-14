<?php
/**
 * Created by PhpStorm.
 * User: trungtnt
 * Date: 24/07/2020
 * Time: 14:39
 */
namespace App\Http\Controllers;


use App\Models\FundDistributorProduct;
use App\Models\FundCompany;
use App\Models\FundProduct;
use App\Services\Transactions\FundProductTransaction;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Traits\TableTrait;

/**
 * @group  FundProduct
 */
class FundProductController extends  Controller
{
    use TableTrait;
    const STATUS_ACTIVE = 1;
    const STATUS_LOCK = 2;


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
     * Fund product list product
     *
     * Danh sách sản phẩm quỹ
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5"
     * @response {
     * "status" : true,
     * "code" : 200,
     * "messages" : "Success",
     * "response" : null
     * }
     */
    public function list (Request $request){
        $query_builder = FundProduct::with('fund_company:id,name','fund_product_type:id,name','fund_certificate:id,name');
        $data = $this->getData($request, $query_builder,'\App\Http\Resources\FundProductResource');
        return $this->responseSuccess($data);
    }

    /**
     * Fund product add product
     *
     * Thêm sản phẩm quỹ
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.fund_company_id integer required ID company of fund. Example: 1
     * @bodyParam params.fund_certificate_id integer required ID Certificate of fund. Example: 1
     * @bodyParam params.fund_product_type_id integer required ID of type product. Example: 1
     * @bodyParam params.name string required Name of product. Example: SIP01
     * @bodyParam params.code string required Code of product. Example: SIP01
     * @bodyParam params.description string Description. Example: "
     * @response {
     * "status" : true,
     * "code" : 200,
     * "messages" : "Success",
     * "response" : {fund_product_id}
     * }
     */
    public function add (Request $request){
        $inputs = $this->validate($request,[
           'params.fund_company_id'      => 'required|integer',
           'params.fund_certificate_id'  => 'required|integer',
           'params.fund_product_type_id' => 'required|integer',
           'params.description'          => 'string',
           'params.name'                 => 'required|string',
           'params.code'                 => 'required|string',
           'params.status'               => 'required|integer',
        ]);
        $inputs['params']['created_by'] = '';
        $product_fund_Auth = Auth::user()->id;
        $inputs['params']['created_by'] = $product_fund_Auth;
        return (new FundProductTransaction())->add($inputs['params'],true);
    }

    /**
     * Fund product update product
     *
     * Cập nhật sản phẩm quỹ
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.fund_product_id integer required ID product. Example: 1
     * @bodyParam params.name string required Name of product. Example: SIP01
     * @bodyParam params.description string Description. Example: "
     * @response {
     * "status" : true,
     * "code" : 200,
     * "messages" : "Success",
     * "response" : null
     * }
     */
    public function update(Request $request){
        $inputs = $this->validate($request,[
            'params.fund_product_id'      => 'required|integer',
            'params.name'                 => 'required|string'
        ]);
        $inputs['params']['update_by'] = '';
        $product_fund_Auth = Auth::user()->id;
        $inputs['params']['update_by'] = $product_fund_Auth;
        return (new FundProductTransaction())->update($inputs['params'],true);
    }

    /**
     * Fund product active product
     *
     * Kích hoạt sản phẩm quỹ
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.fund_product_id integer required ID product. Example: 1"
     * @response {
     * "status" : true,
     * "code" : 200,
     * "messages" : "Success",
     * "response" : null
     * }
     */
    public function active (Request $request){
        $inputs = $this->validate($request,[
            'params.fund_product_id'       => 'required|integer',
        ]);
        return (new FundProductTransaction())->active([
            'fund_product_id' => $inputs['params']['fund_product_id'],
            'updated_by' => auth()->user()->id
        ],true);
    }

    /**
     * Fund product lock product
     *
     * Khóa sản phẩm quỹ
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.fund_product_id integer required ID product. Example: 1"
     * @response {
     * "status" : true,
     * "code" : 200,
     * "messages" : "Success",
     * "response" : null
     * }
     */
    public function lock (Request $request){
        $inputs = $this->validate($request,[
            'params.fund_product_id'       => 'required|integer',
        ]);
        return (new FundProductTransaction())->lock([
            'fund_product_id' => $inputs['params']['fund_product_id'],
            'updated_by' => auth()->user()->id
        ],true);
    }

    /**
     * Fund product get list
     *
     * Lấy danh sách sản phẩm quỹ
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.fund_distributor_id integer required ID product. Example: 1
     * @response {
     * "status": true,
     * "code": 200,
     * "messages": "Success",
     * "response": {
     * }
     */
    public function getList(Request $request)
    {
        $rows = FundProduct::where('status', FundProduct::STATUS_ACTIVE)->get();
        return $this->responseSuccess(FundProduct::getArrayForSelectBox($rows, 'id', 'name'));
    }

    /**
     * Fund Product detail
     *
     * Xem chi tiết thông tin quỹ sản phẩm
     *
     * @bodyPram env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.fund_product_id integer required User ID. Example: 1
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
            'params.fund_product_id' => 'required|integer'
        ]);
        $fund_product= FundProduct::where('id', $inputs['params']['fund_product_id'])->first();
        if ($fund_product) {
            return $this->responseSuccess($fund_product);
        } else {
            $this->error('Loại sản phẩm quỹ không tồn tại');
        }
    }

    /**
     * Fund product get status
     *
     * Lấy danh sách trạng thái người quỹ sản phẩm
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
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
            ['title' => trans('status.user.active'), 'value' => FundProduct::STATUS_ACTIVE],
            ['title' => trans('status.user.lock'), 'value' => FundProduct::STATUS_LOCK],
        ];
        return $this->responseSuccess($status);
    }
}
