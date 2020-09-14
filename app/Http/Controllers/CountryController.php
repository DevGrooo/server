<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Services\Transactions\CountryTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Traits\TableTrait;

/**
 * @group  CountryApi
 */
class CountryController extends Controller
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
     * Country list
     *
     * Danh sách các quốc gia
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
        $query_builder = Country::query();
        $data = $this->getData($request, $query_builder,'\App\Http\Resources\CountryResource');
        return $this->responseSuccess($data);
    }

    /**
     * Country add
     *
     * Thêm sản phẩm quốc gia
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.name string required Name of product. Example: Laos
     * @bodyParam params.code string required Code of product. Example: Laos
     * @bodyParam params.status integer required status of product. Example: 1
     * @response {
     * "status" : true,
     * "code" : 200,
     * "messages" : "Success",
     * }
     */
    public function add (Request $request){
        $inputs = $this->validate($request,[
            'params.name'                 => 'required|string',
            'params.code'                 => 'required|string',
            'params.status'               => 'required|integer',
        ]);
        $inputs['params']['created_by'] = '';
        $country_Auth = Auth::user()->id;
        $inputs['params']['created_by'] = $country_Auth;
        return (new CountryTransaction())->add($inputs['params'],true);
    }

    /**
     *  Country update
     *
     * Cập nhật quốc gia

     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.country_id integer required ID product. Example: 1
     * @bodyParam params.name string required Name of product. Example: Campuchia
     * @bodyParam params.code string Code. Example: Campuchia
     * @response {
     * "status" : true,
     * "code" : 200,
     * "messages" : "Success",
     * "response" : null
     * }
     */
    public function update(Request $request){
        $inputs = $this->validate($request,[
            'params.country_id'      => 'required|integer',
            'params.name'                 => 'required|string',
            'params.code'                 => 'required|string',
        ]);
        $inputs['params']['update_by'] = '';
        $country_Auth = Auth::user()->id;
        $inputs['params']['update_by'] = $country_Auth;
        return (new CountryTransaction())->update($inputs['params'],true);
    }

    /**
     * Country active
     *
     * Kích hoạt  quốc gia
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.country_id integer required ID product. Example: 1"
     * @response {
     * "status" : true,
     * "code" : 200,
     * "messages" : "Success",
     * "response" : null
     * }
     */
    public function active (Request $request){
        $inputs = $this->validate($request,[
            'params.country_id'       => 'required|integer',
        ]);
        return (new CountryTransaction())->active([
            'country_id' => $inputs['params']['country_id'],
            'updated_by' => auth()->user()->id
        ],true);
    }

    /**
     * Country lock
     *
     * Khóa quốc gia
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.country_id integer required ID product. Example: 1"
     * @response {
     * "status" : true,
     * "code" : 200,
     * "messages" : "Success",
     * "response" : null
     * }
     */
    public function lock (Request $request){
        $inputs = $this->validate($request,[
            'params.country_id'       => 'required|integer',
        ]);
        return (new CountryTransaction())->lock([
            'country_id' => $inputs['params']['country_id'],
            'updated_by' => auth()->user()->id
        ],true);
    }

    /**
     * Country detail
     *
     * Xem chi tiết thông tin quốc gia
     *
     * @bodyPram env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.country_id integer required User ID. Example: 1
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
            'params.country_id' => 'required|integer'
        ]);
        $country= Country::where('id', $inputs['params']['country_id'])->first();
        if ($country) {
                return $this->responseSuccess($country);
        } else {
            $this->error('Quốc gia này không được hỗ trợ');
        }
    }

    /**
     * Country get status
     *
     * Lấy danh sách trạng thái quốc gia
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
            ['title' => trans('status.user.active'), 'value' => Country::STATUS_ACTIVE],
            ['title' => trans('status.user.lock'), 'value' => Country::STATUS_LOCK],
        ];
        return $this->responseSuccess($status);
    }

    /**
     * Country get list
     *
     * Lấy danh sách quốc gia
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
    public function getList()
    {
        $rows = Country::where('status', Country::STATUS_ACTIVE)->orderBy('position', 'ASC')->get(['id AS value', 'name AS title']);
        return $this->responseSuccess($rows);
    }
}
