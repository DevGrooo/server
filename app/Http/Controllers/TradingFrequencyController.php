<?php

namespace App\Http\Controllers;

use App\Models\TradingFrequency;
use App\Models\FundCompany;
use App\Services\Transactions\TradingFrequencyTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Traits\TableTrait;

/**
 * @group  TradingFrequencyApi
 */
class TradingFrequencyController extends Controller
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
     * TradingFrequency list
     *
     * Danh sách tần suất giao dịch
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @response {
     * "status" : true,
     * "code" : 200,
     * "messages" : "Success",
     * "response" : null
     * }
     */
    public function list (Request $request){
        $query_builder = TradingFrequency::with('fund_company:id,name');
        $data = $this->getData($request, $query_builder, '\App\Http\Resources\TradingFrequencyResource');
        return $this->responseSuccess($data);
    }
    /**
     * TradingFrequency create
     *
     * Thêm tần suất giao dịch
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.fund_company_id integer required fund company. Example: 1
     * @bodyParam params.type integer required type of trading frequency . Example: 1
     * @bodyParam params.name string required name of trading frequency. Example: Giao dịch 1
     * @bodyParam params.wday integer week  of trading frequency. Example: 1
     * @bodyParam params.mday integer month  of trading frequency. Example: 1
     * @bodyParam params.cut_off_date integer required cut_off_date of trading frequency. Example: 1
     * @bodyParam params.cut_off_hour string required cut_off_hour of trading frequency. Example: 1
     * @bodyParam params.status integer required status of trading frequency. Example: 1
     * @response {
     * "status" : true,
     * "code" : 200,
     * "messages" : "Success",
     * "response" : null
     * }
     */
    public function create (Request $request){
        $inputs = $this->validate($request,[
           'params.fund_company_id' => 'required|integer',
           'params.type'            => 'required|integer',
           'params.name'            => 'required|string|max:255',
           'params.wday'            => 'integer',
           'params.mday'            => 'integer',
           'params.cut_off_date'    => 'required|integer',
           'params.cut_off_hour'    => 'required|string|max:5',
           'params.status'          => 'required|integer',
        ]);
        $inputs['params']['created_by'] = '';
        $trading_Frequency_Auth = Auth::user()->id;
        $inputs['params']['created_by'] = $trading_Frequency_Auth;
        return (new TradingFrequencyTransaction())->create($inputs['params'], true);
    }
    /**
     * Trading Frequency active
     *
     * Mở khóa tần suất giao dịch
     *
     * @bodyPram env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.trading_frequency_id integer required User ID. Example: 1
     *
     * @response {
     * "status" : false,
     * "code" : 403,
     * "messages" : "Access denied",
     * "response" : null
     * }
     */
    public function active (Request $request){
        $inputs = $this->validate($request,[
            'params.trading_frequency_id' => 'required|integer',
        ]);        
        return (new TradingFrequencyTransaction())->active([
            'trading_frequency_id' => $inputs['params']['trading_frequency_id'],
            'updated_by' => auth()->user()->id
        ],true);
    }

    /**
     * Trading Frequency lock
     *
     * Khóa tần suất giao dịch
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.trading_frequency_id integer required ID product. Example: 1"
     * @response {
     * "status" : true,
     * "code" : 200,
     * "messages" : "Success",
     * "response" : null
     * }
     */
    public function lock (Request $request){
        $inputs = $this->validate($request,[
            'params.trading_frequency_id'       => 'required|integer',
        ]);
        return (new TradingFrequencyTransaction())->lock([
            'trading_frequency_id' => $inputs['params']['trading_frequency_id'],
            'updated_by' => auth()->user()->id
        ],true);
    }

    /**
     * Trading Frequency get status
     *
     * Lấy danh sách trạng thái tần suất giao dịch
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
            ['title' => trans('status.trading_frequency.active'), 'value' => TradingFrequency::STATUS_ACTIVE],
            ['title' => trans('status.trading_frequency.lock'), 'value' => TradingFrequency::STATUS_LOCK],
        ];
        return $this->responseSuccess($status);
    }
}
