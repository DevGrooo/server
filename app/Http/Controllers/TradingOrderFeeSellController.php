<?php


namespace App\Http\Controllers;


use App\Http\Controllers\Traits\TableTrait;
use App\Models\TradingOrderFeeSell;
use App\Services\Transactions\TradingOrderFeeSellTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TradingOrderFeeSellController extends Controller
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
     * Order list trading fee sell
     *
     * Danh sách phí bán
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
     * "response": null
     * }
     */
    public function list(Request $request)
    {
        $query_builder = TradingOrderFeeSell::with('fund_product:id,name');
        $data = $this->getData($request, $query_builder, '\App\Http\Resources\TradingOrderFeeSellResource');
        return $this->responseSuccess($data);
    }

    /**
     * Order add trading fee sell
     *
     * Thêm phí bán
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.fund_product_id integer require ID fund product id. Example: 1
     * @bodyParam params.fund_distributor_id integer ID fund distributor. Example:
     * @bodyParam params.investor_id integer ID investor. Example:
     * @bodyParam params.start_at string required Start apply fee. Example: 2020-07-10 13:00:00
     * @bodyParam params.lines.*.holding_period integer required Holding period. Example: 365
     * @bodyParam params.lines.*.fee_amount double required Fee amount. Example: 10000
     * @bodyParam params.lines.*.fee_percent float required Fee percent. Example: 0.01
     *
     * @response {
     * "status": true,
     * "code": 200,
     * "messages": "Success",
     * "response": {trading_order_fee_sell_id}
     * }
     */
    public function create(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.fund_product_id' => 'required|integer',
            'params.start_at' => 'required',
            'params.lines.*.holding_period' => 'required|integer',
            'params.lines.*.fee_amount' => 'required',
            'params.lines.*.fee_percent' => 'required',
        ]);
        $user = Auth::user();
        $inputs['params']['created_by'] = $user->id;
        return (new TradingOrderFeeSellTransaction())->createMulti($inputs['params'], true);
    }

    /**
     * Order lock trading fee sell
     *
     * Khóa phí bán
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.trading_order_fee_sell_id integer required Fee buy Id. Example: 1
     *
     * @response {
     * "status": true,
     * "code": 200,
     * "messages": "Success",
     * "response": null
     * }
     */
    public function lock(Request $request)
    {
        $inputs = $this->validate($request, [
            'params.trading_order_fee_sell_id' => 'required|integer'
        ]);

        return (new TradingOrderFeeSellTransaction())->lock($inputs['params'], true);
    }

    /**
     * Trading order fee sell get status
     *
     * Lấy danh sách trạng thái phí bán
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
        $list_status = TradingOrderFeeSell::getListStatus();
        return $this->responseSuccess(TradingOrderFeeSell::getArrayForSelectBox($list_status));
    }
}
