<?php


namespace App\Http\Controllers;


use App\Http\Controllers\Traits\TableTrait;
use App\Models\Investor;
use App\Models\TradingOrder;
use App\Services\Transactions\TradingOrderTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TradingOrderController extends Controller
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
     * List trading order
     *
     * Danh sách lệnh giao dịch
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     *
     * "@response {
     * "status": true,
     * "code": 200,
     * "messages": "Success",
     * "response": null
     * }
     */

    public function list (Request $request)
    {
        $query_builder = TradingOrder::query();
        $data = $this->getData($request, $query_builder, '\App\Http\Resources\TradingOrderResource');
        return $this->responseSuccess($data);
    }

    /**
     * Add trading order
     *
     * Thêm lệnh giao dịch
     *
     * @bodyParam env string required Enviroment client. Example: web
     * @bodyParam app_version string required App version. Example: 1.1
     * @bodyParam lang string required Language. Example: vi
     * @bodyParam token string required Token code. Example: c842eb6ee8372226d267f1495d0634d5
     * @bodyParam params.investor_id integer required . Example: 1
     * @bodyParam params.trading_account_number string required. Example: 12345241
     * @bodyParam params.sip_id integer required . Example: 1
     * @bodyParam params.deal_type string required . Example: NS
     * @bodyParam params.send_amount string required. Example: 10.000.000
     * @bodyParam params.send_currency string. Example: 2132141
     * @bodyParam params.receive_currency string. Example: 21311312
     * @bodyParam params.receive_fund_product_id id required. example: 1
     * @response {
     * "status": true,
     * "code": 200,
     * "messages": "Success",
     * "response": null
     * }
     */
    public function create(Request $request)
    {
        $inputs = $this->validate($request, [
           'params.investor_id'            => 'required|integer',
           'params.sip_id'                 => 'integer',
           'params.deal_type'              => 'required|string',
           'params.sub_amount'            => 'required|string',
           'params.send_currency'          => 'string',
           'params.receive_currency'       => 'string',
           'params.fund_product_id' => 'integer'
        ]);
        $investor = Investor::where('id', $inputs['params']['investor_id'])->first();
        $inputs['params']['trading_account_number'] = $investor->trading_account_number;
//        dd( $inputs['params']['trading_account_number']);
        $user = Auth::user();
        $inputs['params']['created_by'] = $user->id;
        if ($inputs['params']['deal_type'] == TradingOrder::DEAL_TYPE_BUY_NORMAL) {
            $inputs['params']['sip_id'] = 0;
            $inputs['params']['send_currency'] = 'VND';
            $inputs['params']['receive_fund_product_id'] = $inputs['params']['fund_product_id'];
            $inputs['params']['send_amount'] = $inputs['params']['sub_amount'];
            unset($inputs['params']['fund_product_id']);
            unset($inputs['params']['sub_amount']);
            return (new  TradingOrderTransaction())->processBuy($inputs['params'], true);
        }
        if ($inputs['params']['deal_type'] == TradingOrder::DEAL_TYPE_SELL_NORMAL) {
            $inputs['params']['sip_id'] = 0;
            $inputs['params']['receive_currency'] = 'VND';
            $inputs['params']['send_fund_product_id'] = $inputs['params']['fund_product_id'];
            $inputs['params']['send_amount'] = $inputs['params']['sub_amount'];
            unset($inputs['params']['sub_amount']);
            unset($inputs['params']['fund_product_id']);
            return (new  TradingOrderTransaction())->createSell($inputs['params'], true);
        }
    }

}
