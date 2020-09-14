<?php

namespace App\Http\Controllers;

use App\Models\TradingSession;
use App\Services\Transactions\TradingSessionTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Traits\TableTrait;

/**
 * @group  TradingSessionApi
 */
class TradingSessionController extends Controller
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
     * TradingSession list
     *
     * Danh sách các giao dịch
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
        $query_builder = TradingSession::query();
        $data = $this->getData($request, $query_builder,'\App\Http\Resources\TradingSessionResource');
        return $this->responseSuccess($data);
    }

    /**
     * TradingSession cancer
     *
     * Hủy các giao dịch
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5"
     * @bodyParam params.trading_session_id integer required ID TradingSession. Example: 1
     * @response {
     * "status" : true,
     * "code" : 200,
     * "messages" : "Success",
     * "response" : null
     * }
     */
    public function cancel (Request $request){
        $inputs = $this->validate($request, [
            'params.trading_session_id' => 'required|integer'
        ]);
        return (new TradingSessionTransaction())->cancel([
            'trading_session_id' => $inputs['params']['trading_session_id'],
            'updated_by' => auth()->user()->id
        ],true);
    }
}
